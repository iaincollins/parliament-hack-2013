<?php

class Bill {

    // This is only being used for the hack day. Please don't steal it or I'll be sad :(
    const THEYWORKFORYOU_API_KEY = 'GfmMVnCm29fQEqvFS7CgLHLJ';

    /**
     * @var string  GUID based on URL (that doesn't changes and intended to be used as a GUID)
     */
    public $id;

    /**
     * @var string  The link to the bill on services.parliament.uk
     */
    public $url;

    /**
     * @var string  The name of the bill
     */
    public $title;
    
    /**
     * @var \Member[int]    Members who are sponsoring this bill.
     */
    public $members = array();

    /**
     * @var string  A description of the bill (typically 500-1000 words)
     */
    public $description;

    /**
     * @var Int     Reflects the current stage of the bill
     *
     * 0 = House of Commons, First Reading
     * 1 = House of Commons, Second Reading
     * 2 = House of Commons, Committee Stage
     * 3 = House of Commons, Review Stage
     * 4 = House of Commons, Third Reading
     * 5 = House of Lords, First Reading
     * 6 = House of Lords, Second Reading
     * 7 = House of Lords, Committee Stage
     * 8 = House of Lords, Review Stage
     * 9 = House of Lords, Third Reading
     * 10 = Consideration of amendments
     * 11 = Royal assent
     */
    public $stage;

    /**
     * @var \Bill\Type     The type of bill (e.g. Government Bill, Private Members' Bill)
     */
    public $type;
    
    /**
     * @var \Events[int]    Events (such as debates) scheduled which relate to this bill.
     */
    public $events;

    /**
     * @var string[int] A list of categories the bill has been tagged with (e.g. "Railways", "Buses")
     */
    public $tags = array();
    
    /**
     * @var string  The HTML for the page for this bill on the parliament website (for scraping)
     */
    private $infoPageHtml;

    public function getInfoPageHtml() {
        if ($this->infoPageHtml != null)
            return $this->infoPageHtml;

        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $this->infoPageHtml = $response;
        
        return $this->infoPageHtml;
    }
    
    /**
     * @return string The type of bill
     */
    public function getBillType() {
        $infoPageHtml = $this->getInfoPageHtml();
        try {
            $matches = array();
            preg_match("/\<dl class=\"bill-agents\"\>(.*)\<\/dl\>/s", $infoPageHtml, $matches);
            
            $simpleXml = @simplexml_load_string($matches[0]);

            if (!$simpleXml)
                return "";
            
            $i = 0;
            foreach ($simpleXml->xpath('//dd') as $billType) {
                // It's always the first entry
                return $billType;
            }
        } catch (\Exception $ex) { }
        return false;
    }
    
    /**
     * @return string[int] Returns a list of the members that sponsored this bill
     */
    public function getMembers() {

        if (count($this->members) > 0)
            return $this->members;

        $infoPageHtml = $this->getInfoPageHtml();

        $memberNames = array();
        // Get member names
        try {
            $matches = array();
            preg_match("/\<dl class=\"bill-agents\"\>(.*)\<\/dl\>/s", $infoPageHtml, $matches);
            $simpleXml = @simplexml_load_string($matches[0]);
            
            if (!$simpleXml)
                return $memberNames;

            $i = 0;
            foreach ($simpleXml->xpath('//dd') as $memberName) {
                // Ignore first entry
                if ($i != 0)
                    array_push($memberNames, (string) $memberName);
                $i++;
            }
        } catch (\Exception $ex) { }
        
        foreach ($memberNames as $memberName) {
            array_push($this->members, \Member::getMemberByName($memberName));
        }
        
        return $this->members;
    }

    public function getEvents($limit = 20) {
    
        if (is_array($this->events))
            return $this->events;
        
        $this->events = array();
        
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, 'http://www.theyworkforyou.com/api/getHansard?key='.self::THEYWORKFORYOU_API_KEY.'&search='.urlencode(trim($this->title)).'&output=js');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        $i = 0;
        $events = array();
        if (!isset($response->rows))
            return $events;
            
        foreach ($response->rows as $result) {

            // Ignore malformed results
            if (!isset($result->title) || !isset($result->event_date) || !isset($result->link_external))
                continue;
                
            $event = new Event();
            $event->name = $result->title;
            $event->date = $result->event_date; 
            $event->url = $result->link_external;
            $events[strtotime($result->event_date)] = $event;
            $i++;
            if ($i > $limit)
                break;
        }
        
        ksort($events);
        
        $this->events = $events;
        
        return $this->events;
    }
    
   
    
    public function getBillTextUrl() {
        $infoPageHtml = $this->getInfoPageHtml();

        // If you accidentally look at this, I suggest pretending you haven't seen it.
        $matches = array();
        preg_match("/\<td class=\"bill-item-description\"\>\<a href=\".*\"/", $infoPageHtml, $matches);
        
        // If no URL that means there is no bill text to download yet (boo)
        if (!isset($matches[0]))
            return false;
            
        $contentsUrl = $matches[0];
        $contentsUrl = preg_replace("/\<td class=\"bill-item-description\"\>\<a href=\"/", '', $contentsUrl);
        $contentsUrl = preg_replace("/\"/", '', $contentsUrl);
        
        return $contentsUrl;
    }
    
    /**
     * Get the text of the most recent draft of the bill.
     *
     * Does this by doing regexes on HTML to get the URLs of pages to do
     * more regexes on HTML and then stitches them together.
     *
     * OH GOD I'M PARSING HTML WITH REGEX.
     * @fixme Remove this before Tony The Pony comes for me.
     */
    public function getBillText() {
    
        $contentsUrl = $this->getBillTextUrl();
        
        // If no URL that means there is no bill text to download yet (boo)
        if ($contentsUrl == false)
            return false;

        // Base URL for all pages same as the first URL, but with different file name,
        // so we want to strip that.
        $baseUrl = $contentsUrl;
        $baseUrl = preg_replace("/[^\/]*$/", '', $baseUrl);
        
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $contentsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);

        // Get all the HTML pages for the bill from the page control
        $matches = array();
        preg_match("/\<p class=\"LegNavTextBottom\"\>.*\<\/p\>/", $response, $matches);
        
        if (!isset($matches[0]))
            return false;

        $billPages = array();        
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        if (preg_match_all("/$regexp/siU", $matches[0], $matches, PREG_SET_ORDER)) {
            foreach($matches as $match) {
                array_push($billPages, $baseUrl.$match[2]);
            }
        }
        
        // Get the stripped HTML for each seperate page and stitch them all together
        $billText = '';
        foreach ($billPages as $url) {
            $billText .= $this->getBillPageHtml($url);
        }

        return $billText;
    }

    /**
     * Get the HTML body for a page of a bill, without header of footer.
     * Super janky. Suspect it eats some text.
     * @fixme Parse the page properly.
     */
    public function getBillPageHtml($url) {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);

        // Just focus on the main content (stripping the header)
        $matches = array();
        preg_match("/\<div class=\"LegContent\"\>(.*)\<\/div\>/s", $response, $matches);
        $html = $matches[0];
        
        // Remove this clear div and all the footer stuff after it
        $html = preg_replace("/\<div class=\"LegNavClear\"\>.*/s", '', $html);
        
        // No links please, we're hackers.
        $html = preg_replace("/\<a(.*?)href=\"(.*?)\"(.*?)\>/s", '', $html);
        $html = preg_replace("/\<a\/\>/s", '', $html);
        
        return $html;
    }
    
    /**
     * Get the URL for the PDF of the latest version of the page by screen scraping.
     */
    public function getPdfUrl() {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);

        // If you accidentally look at this, I suggest pretending you haven't seen it.
        $matches = array();
        preg_match("/\<span class=\"application-pdf\"\>.*\<\/span\>/", $response, $matches);
        
        // If no PDF URL that means there is no bill to download yet (boo)
        if (!isset($matches[0]))
            return false;
            
        $pdfUrl = $matches[0];
        $pdfUrl = str_replace('<span class="application-pdf"><a href="', '', $pdfUrl);
        $pdfUrl = preg_replace("/\"\>PDF version,(.*)\<\/a\>\<\/span\>/", '', $pdfUrl);
        // Forcably fix some incorrectly parsed URLs
        $pdfUrl = preg_replace("/^.*http\:/", 'http:', $pdfUrl);
        $pdfUrl = preg_replace("/\.pdf.*$/", '.pdf', $pdfUrl);
                
        return $pdfUrl;
    }
    
    public static function getBillById($id) {
        // @fixme Infinate monkey indexing.
        foreach (Bills::getAllBillsBeforeParliament() as $bill) {
            if ($id == $bill->id)
                return $bill;
        }
    }

}
?>