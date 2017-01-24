<?php
session_start();
require_once("twitteroauth/twitteroauth/twitteroauth.php"); //Path to twitteroauth library
 
$twitteruser = "plazathemes";
$notweets = 30;
$consumerkey = "qulMEXc9RpNgvdHniZsKCQ";
$consumersecret = "9Wk7UwrlfkeR8BaKU1Nz7gS6Y3wQ2oMAuRTSPdwSpo";
$accesstoken = "167448460-MuUwtTxWoehX4MKL8KrEbP6pkLnsQf0p3NKuiUGz";
$accesstokensecret = "DKVQipT6cdOpnRELDxlsC3Mf5Rf20TA2IdUU6dzaqg";
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
  
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
 
$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);
 
echo json_encode($tweets);
?>