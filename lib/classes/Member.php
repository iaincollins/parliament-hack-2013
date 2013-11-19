<?php
/**
 * An object representing a member of Parliament (an MP or Lord)
 * @version 1.0
 */
class Member {

    // @fixme This key shouldn't be here.
    // I'm going to remove it from here in future, for now please don't use it!
    const THEYWORKFORYOU_API_KEY = 'GfmMVnCm29fQEqvFS7CgLHLJ';

    const MEMBER_CACHE_DIR = '/tmp/members/';
    
    /**
     * @var string  The name of the member
     */
    public $name;
    
    /**
     * @var string  A URL to their profile page.
     */
    public $url;

    /**
     * @var string  The party they are a member of
     */
    public $party;
    
    /**
     * @var string  The URL to an image of the member (with fallback placeholder in case no image available)
     */
    public $avatar = 'http://www.gravatar.com/avatar/00000000000000000000000000000000?d=identicon&f=y';
    
    public static function getMemberByName($name) {

        $name = (string) $name;
        
        if (!file_exists(self::MEMBER_CACHE_DIR))
            mkdir(self::MEMBER_CACHE_DIR);

        $memberCache = self::MEMBER_CACHE_DIR.sha1(trim($name)).'.cache';
        
        // Use cache if it exists and is not older than 48 hours.
        if (file_exists($memberCache) && strtotime('+48 hours', filemtime($memberCache)) > time())
            return unserialize(file_get_contents($memberCache));

        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, 'http://data.parliament.uk/membersdataplatform/services/mnis/members/query/name*'.urlencode(trim($name))."/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);

        // To get the avtar we want to use theyworkfor you, but they don't allow look up by name
        // or any of the 3 ID's parliament use, but they DO allow you to look up by constituency
        $simpleXml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?>'.$response);

        $constituency = $simpleXml->Member->MemberFrom;

        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, 'http://www.theyworkforyou.com/api/getMP?constituency='.urlencode(trim($constituency)).'&key='.self::THEYWORKFORYOU_API_KEY.'&output=json_decode()');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $response = json_decode($response);

        $member = new self();
        $member->name = (string) $name;
        
        // Use DOD avatar if possible (these are generally better)
        if ($simpleXml->Member['Dods_Id'])
            $member->avatar = 'http://www.dodonline.co.uk/photos/'.(string) $simpleXml->Member['Dods_Id'].'.jpg';
            
        if (isset($response->full_name))
            $member->name = (string) $response->full_name;
            
        if (isset($response->url))
            $member->url = 'http://www.theyworkforyou.com'.(string) $response->url;
            
        if (isset($response->party))
            $member->party = (string) $response->party;
            
        if (!$member->avatar && isset($response->image))
            $member->avatar = 'http://www.theyworkforyou.com'.(string) $response->image;

        // Cache member object
        file_put_contents($memberCache, serialize($member));

        return $member;
    }
    
}
?>