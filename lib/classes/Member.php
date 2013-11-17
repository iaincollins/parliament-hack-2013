<?php

class Member {

    public $name;
    public $url;
    public $party;
    public $avatar = 'http://www.gravatar.com/avatar/00000000000000000000000000000000?d=identicon&f=y';
    
    public static function getMemberByName($name) {
    
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

        // This is only being published for the hack day. Please don't steal it or I'll be sad :(
        $apiKey = 'GfmMVnCm29fQEqvFS7CgLHLJ';
        
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, 'http://www.theyworkforyou.com/api/getMP?constituency='.urlencode(trim($constituency)).'&key='.$apiKey.'&output=json_decode()');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $response = json_decode($response);

        $member = new self();
        $member->name = $name;
        if ($simpleXml->Member['Dods_Id'])
            $member->avatar = 'http://www.dodonline.co.uk/photos/'.$simpleXml->Member['Dods_Id'].'.jpg';
        if (isset($response->full_name))
            $member->name = $response->full_name;
        if (isset($response->url))
            $member->url = 'http://www.theyworkforyou.com'.$response->url;
        if (isset($response->party))
            $member->party = $response->party;
        if (!$member->avatar && isset($response->image))
            $member->avatar = 'http://www.theyworkforyou.com'.$response->image;
            
        return $member;
    }
    
}
?>