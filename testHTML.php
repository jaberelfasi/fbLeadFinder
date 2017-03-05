<?php
require(__DIR__.'/FacebookSession.php');

$fb = new FacebookSession();
//$fb->addFacebookPages("books");
$fb->grabInfoFromPage('http://www.facebook.com/1471979989726231');