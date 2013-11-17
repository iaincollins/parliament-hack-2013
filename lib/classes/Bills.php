<?php

class Bills {
    
    public static function getBills() {
    
        // @fixme Loading RSS feed from flat file for now
        $rssFeedXml = file_get_contents(dirname(__FILE__).'/../bills.xml');
        $rssFeedXml = str_replace('p4:stage', 'stage', $rssFeedXml);

        $rssFeed = simplexml_load_string($rssFeedXml);

        $billTypes = array("Government Bill",
                           "Private Members' Bill (Ballot Bill)",
                           "Private Members' Bill (Presentation Bill)",
                           "Private Members' Bill (under the Ten Minute Rule, SO No 23)",
                           "Private Members' Bill (Starting in the House of Lords)",
                           "Private Members' Bill (Starting in the House of Lords)",
                           "Private Bill"
                            );
             
        $bills = array();
        foreach ($rssFeed->channel->item as $item) {
        
            ;
            $bill = new Bill();
            $bill->id = sha1($item->guid);
            $bill->url = trim($item->link);
            $bill->title = trim($item->title);
            $bill->description = trim($item->description);
            $bill->description = preg_replace("/;.*/s", ".", $bill->description);
            
            $categories = array();
            foreach ($item->category as $category) {
                array_push($categories, $category);
            }

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

            // NB: Don't have examples for the value should be for the following two states
            /*
             * 10 = Consideration of amendments
             * 11 = Royal assent                 
            */
            
            foreach ($billTypes as $billTypeId => $billType) {
                if (in_array($billType, $categories)) {
                    $bill->type = $billTypeId;
                    if (($key = array_search($billType, $categories)) !== false)
                        unset($categories[$key]);
                }
            }
            
            $bill->tags = $categories;
            array_push($bills, $bill);
        }
        
        return $bills;
    }
}
?>