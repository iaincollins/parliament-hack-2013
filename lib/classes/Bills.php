<?php

class Bills {

    const ALL_BILLS_BEFORE_PARLIAMENT_RSS_FEED = 'http://services.parliament.uk/bills/AllBills.rss';
    const ALL_BILLS_BEFORE_PARLIAMENT_CACHE = '/tmp/bills.cache';
    
    public static function getAllBillsBeforeParliament() {    
        // If cache doesn't exist or is older than 12 hours then update it
        if (!file_exists(self::ALL_BILLS_BEFORE_PARLIAMENT_CACHE) ||
             strtotime('+12 hours', filemtime(self::ALL_BILLS_BEFORE_PARLIAMENT_CACHE)) < time()) {
            $bills = self::getAllBillsBeforeParliamentFromRSSFeed();
            file_put_contents(self::ALL_BILLS_BEFORE_PARLIAMENT_CACHE, serialize($bills));
            return $bills;
        } else {
            return unserialize(file_get_contents(self::ALL_BILLS_BEFORE_PARLIAMENT_CACHE));
        }                    
    }
    
    public static function getAllBillsBeforeParliamentFromRSSFeed() {
    
        // @fixme Loading RSS feed from flat file for now
        $rssFeedXml = file_get_contents(self::ALL_BILLS_BEFORE_PARLIAMENT_RSS_FEED);

        //$rssFeedXml = file_get_contents(dirname(__FILE__).'/../bills.xml');
        
        // Hackily change the 'stage' attribute name so we can easily parse it with SimpleXml
        $rssFeedXml = str_replace('p4:stage', 'stage', $rssFeedXml);

        $rssFeed = simplexml_load_string($rssFeedXml);

        $billTypes = \Bill\Type::getAllBillTypes();
        $bills = array();
        foreach ($rssFeed->channel->item as $item) {

            $bill = new Bill();
            $bill->id = sha1($item->guid);
            $bill->url = trim($item->link);
            $bill->title = trim($item->title);
            $bill->description = trim($item->description);
            
            // Trigger pre-emptive fetching of members assoicated with the bill (so it's cached)
            $bill->getMembers();

            // Trigger pre-emptive fetching of events assoicated with the bill (so it's cached)
            $bill->getEvents();
            
            $categories = array();
            foreach ($item->category as $category) {
                array_push($categories, (string) $category);
            }

            // To determine the stage of the bill we have to check both all
            // <category> elements and the 'stage' attribute (not ideal).
            if (in_array('Commons', $categories)) {
                if ($item['stage'] == "1st reading")
                    $bill->stage = 0;
                if ($item['stage'] == "2nd reading")
                    $bill->stage = 1;
                if ($item['stage'] == "Committee stage")
                    $bill->stage = 2;
                if ($item['stage'] == "Review stage")
                    $bill->stage = 3;
                if ($item['stage'] == "3rd reading")
                    $bill->stage = 4;
                if (($key = array_search('Commons', $categories)) !== false)
                    unset($categories[$key]);
            } else if (in_array('Lords', $categories)) {
                if ($item['stage'] == "1st reading")
                    $bill->stage = 5;
                if ($item['stage'] == "2nd reading")
                    $bill->stage = 6;
                if ($item['stage'] == "Committee stage")
                    $bill->stage = 7;
                if ($item['stage'] == "Review stage")
                    $bill->stage = 8;
                if ($item['stage'] == "3rd reading")
                    $bill->stage = 9;
                if (($key = array_search('Lords', $categories)) !== false)
                    unset($categories[$key]);
            } else if (in_array('Not assigned', $categories)) {
                if (($key = array_search('Not assigned', $categories)) !== false)
                    unset($categories[$key]);
            }

            // @fixme I need examples of the stage value for the following two states:
            // 10 = Consideration of amendments
            // 11 = Royal assent
            
            // Get the type of bill.
            foreach ($billTypes as $billType) {
                if (in_array($billType->rssFeedValue, $categories)) {
                    $bill->type = $billType;
                    
                    if (($key = array_search($billType->rssFeedValue, $categories)) !== false)
                        unset($categories[$key]);
                }
            }
            
            // Add the (friendly name) version of this type of bill as a tag
            array_push($bill->tags, $bill->type->name);
            
            // Any renaming "Category" labels get converted into tags.
            foreach ($categories as $category) {                    
                array_push($bill->tags, $category);
            }
            
            array_push($bills, $bill);
            
        }
        
        return $bills;
    }
}
?>