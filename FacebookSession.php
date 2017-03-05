<?php

require_once __DIR__ . '/sdk/src/Facebook/autoload.php';
require(__DIR__ . '/Dao.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FacebookSession {
    public $fb;
    public $helper;
    public $permissions;
    public $accessToken;
    public $oAuth2Client;
    public $longLivedAccessToken;
    public $user;
    public $search;
    public $loginUrl;
    public function openFacebookSession($keyword) {
        session_start();

        $this->fb = new Facebook\Facebook([
            'app_id' => '403136806704456',
            'app_secret' => '990147c6af6d115aeaa0f477368186cf',
            'default_graph_version' => 'v2.8'
        ]);

        $this->helper = $this->fb->getRedirectLoginHelper();

// app directory could be anything but website URL must match the URL given in the developers.facebook.com/apps
        define('APP_URL', 'http://sandbox.dev:8080/leadFinderFB/search.php');

        $this->permissions = []; // optional

        try {
            if (isset($_SESSION['facebook_access_token'])) {
                $this->accessToken = $_SESSION['facebook_access_token'];
            } else {
                $this->accessToken = $this->helper->getAccessToken();
            }
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: line 45' . $e->getMessage();
            echo "<br><br><br>--------<br>line 46";
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            echo "<br><br><br>--------<br>line 51";
            exit;
        }

        if (isset($this->accessToken)) {
            if (isset($_SESSION['facebook_access_token'])) {
                $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            } else {
                // getting short-lived access token
                $_SESSION['facebook_access_token'] = (string) $this->accessToken;

                // OAuth 2.0 client handler
                $this->oAuth2Client = $this->fb->getOAuth2Client();

                // Exchanges a short-lived access token for a long-lived one
                $this->longLivedAccessToken = $this->oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);

                $_SESSION['facebook_access_token'] = (string) $this->longLivedAccessToken;

                // setting default access token to be used in script
                $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            }

            // redirect the user back to the same page if it has "code" GET variable
            if (isset($_GET['code'])) {
                header('Location: ./');
            }

            // validating user access token
            try {
                $this->user = $this->fb->get('/me');
                $this->user = $this->user->getGraphNode()->asArray();
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                echo "<br><br><br>--------<br>line 86";
                session_destroy();
                // if access token is invalid or expired you can simply redirect to login page using header() function
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                echo "<br><br><br>--------<br>line 93";
                exit;
            }

            $search = $this->fb->get('/search?q=' . $keyword . '&type=page&limit=1000');
        $search = $search->getGraphEdge()->asArray();

        $fillDb = new Dao("sandbox.dev", "root", "addpeople", "facebook");
        $countArray = $fillDb->addFacebookPages($search);
        echo "<br><br><br>---------<br>number of inserts where " . $countArray[1];
        echo "<br>---------<br>number of errors where " . $countArray[0];
        echo "<pre>";
        print_r($search);
        echo "</pre>";
            return $this->fb;
        } else {
            // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
            $this->loginUrl = $this->helper->getLoginUrl(APP_URL, $this->permissions);
            echo '<a href="' . $this->loginUrl . '">Log in with Facebook!</a>';
        }
    }

    public function addFacebookPages($keyword) {
        // type can be user, group, page or event
        $this->openFacebookSession($keyword);
        
    }
    

}
