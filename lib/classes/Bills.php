<?php

class Bills {
    
    public static function getBills() {
    
        // @fixme Loading RSS feed from flat file for now
        $rssFeed = simplexml_load_file(dirname(__FILE__).'/../bills.xml');

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
                if (($key = array_search('Commons', $categories)) !== false)
                    unset($categories[$key]);
            } else if (in_array('Lords', $categories)) {
                if (($key = array_search('Lords', $categories)) !== false)
                    unset($categories[$key]);
            } else if (in_array('Not assigned', $categories)) {
                if (($key = array_search('Not assigned', $categories)) !== false)
                    unset($categories[$key]);
            }

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
        
//        die(print_r($bills,1));
        
        return $bills;
    }
}
?>