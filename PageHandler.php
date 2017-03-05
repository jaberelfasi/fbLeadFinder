<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PageHandler {

    public $fbPageLinks = array();
    public $rowCount = 0;

    public function generatePages($server, $user, $pass, $db) {
        $grabfromDB = new Dao($server, $user, $pass, $db);
//$fbPageLinks = array();
//pass $rowCount by referrence to get number of records retrieved
        $idArray = $grabfromDB->grabFacebookPagesIDs($this->rowCount);
        for ($i = 0; $i < $this->rowCount; $i++) {
            $generatePage = "http://www.facebook.com/" . $idArray[$i];
            array_push($this->fbPageLinks, $generatePage);
            return $this->fbPageLinks;
        }
    }

    public function printPages() {
        for ($i = 0; $i < $this->rowCount; $i++) {
            echo "<br>" . $this->fbPageLinks[$i] . " (from object)";
        }
    }

    public function grabInfoFromPages() {
        
    }

    function cURL($url, $header = NULL, $cookie = NULL, $p = NULL) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, $header);
        curl_setopt($ch, CURLOPT_NOBODY, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        if ($p) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
        }
        $result = curl_exec($ch);

        if ($result) {
            return $result;
        } else {
            return curl_error($ch);
        }
        curl_close($ch);
    }

    
    
}
