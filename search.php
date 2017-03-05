<?php
session_start();
require_once __DIR__ . '/sdk/src/Facebook/autoload.php';
require(__DIR__.'/Dao.php');

$fb = new Facebook\Facebook([
  'app_id' => '403136806704456',
  'app_secret' => '990147c6af6d115aeaa0f477368186cf',
  'default_graph_version' => 'v2.8'
  ]);

$helper = $fb->getRedirectLoginHelper();

// app directory could be anything but website URL must match the URL given in the developers.facebook.com/apps
define('APP_URL', 'http://sandbox.dev:8080/leadFinderFB/search.php');

$permissions = []; // optional
	
try {
	if (isset($_SESSION['facebook_access_token'])) {
		$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 	// When Graph returns an error
 	echo 'Graph returned an error: ' . $e->getMessage();

  	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
 	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
 }

if (isset($accessToken)) {
	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		// getting short-lived access token
		$_SESSION['facebook_access_token'] = (string) $accessToken;

	  	// OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();

		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);

		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;

		// setting default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}

	// redirect the user back to the same page if it has "code" GET variable
	if (isset($_GET['code'])) {
		header('Location: ./');
	}

	// validating user access token
	try {
		$user = $fb->get('/me');
		$user = $user->getGraphNode()->asArray();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		session_destroy();
		// if access token is invalid or expired you can simply redirect to login page using header() function
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	// type can be user, group, page or event
	$search = $fb->get('/search?q=t-shirts&type=page&limit=1000');
	$search = $search->getGraphEdge()->asArray();
        
        
        $fillDb = new Dao("sandbox.dev", "root", "addpeople", "facebook");
        $countArray = $fillDb->addFacebookPages($search);
        echo "<br><br><br>---------<br>number of inserts where ".$countArray[1];
        echo "<br>---------<br>number of errors where ".$countArray[0];
        echo "<pre>";
        print_r($search);
        echo "</pre>";
        /*
	foreach ($search as $key) {
		echo $key['name'] . '<br>';
	}*/
  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
} else {
	// replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
	$loginUrl = $helper->getLoginUrl(APP_URL, $permissions);
	echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
}
