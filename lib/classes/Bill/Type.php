<?php
/**
 * Defines a type of bill;
 *
 *  @version 1.0
 */
namespace Bill;

class Type {
    
    /**
     * @var int     Unique ID for the type of bill
     */
    public $id;
    
    /**
     * @var string  A user friendly name for the type of bill
     */
    public $name;
    
    /**
     * @var string  A description for this type of bill.
     *
     * @todo Not currently populated yet.
     */
    public $description;
    
    /**
     * @var string  The value used for this type of bill in the RSS feed
     */
    public $rssFeedType;
    
    public static function getBillTypeById($id) {
        $bills = self:: getAllBillTypes();
        if (array_key_exists($id, $bills))
            return $bills[$id];
        
        return null;
    }
    
    public static function getBillTypeByRssFeedValue($rssFeedValue) {
        foreach (self:: getAllBillTypes() as $bill) {
            if ($bill->rssFeedValue == $rssFeedValue)
                return $bill;
        }
        return null;
    }
    
    /**
     * The list of types of bills is fairly small and so hard coded here.
     *
     * @return  \Bill\Type[int]     Returns a list of bill types as an array.
     */
    public static function getAllBillTypes() {

        $bills = array();
        
        $bill = new self();
        $bill->id = 0;
        $bill->name = "Government Bill";
        $bill->rssFeedValue = "Government Bill";
        array_push($bills, $bill);

        $bill = new self();
        $bill->id = 1;
        $bill->name = "Ballot Bill";
        $bill->rssFeedValue = "Private Members' Bill (Ballot Bill)";
        array_push($bills, $bill);

        $bill = new self();
        $bill->id = 2;
        $bill->name = "Presentation Bill";
        $bill->rssFeedValue = "Private Members' Bill (Presentation Bill)";
        array_push($bills, $bill);

        $bill = new self();
        $bill->id = 3;
        $bill->name = "Ten Minute Rule Bill";
        $bill->rssFeedValue = "Private Members' Bill (under the Ten Minute Rule, SO No 23)";
        array_push($bills, $bill);

        $bill = new self();
        $bill->id = 4;
        $bill->name = "From the House of Lords";
        $bill->rssFeedValue = "Private Members' Bill (Starting in the House of Lords)";
        array_push($bills, $bill);

        $bill = new self();
        $bill->id = 5;
        $bill->name = "Private Bill";
        $bill->rssFeedValue = "Private Bill";
        array_push($bills, $bill);

        // @todo Verify this rssFeedValue for Hybrid Bills
        $bill = new self();
        $bill->id = 6;
        $bill->name = "Hybrid Bill";
        $bill->rssFeedValue = "Hybrid Bill";
        array_push($bills, $bill);

        return $bills;
    }
}
