<?php
ini_set('max_execution_time', 0);
session_start();

require_once 'apiClient.php';
require_once 'contrib/apiLatitudeService.php';

$client = new apiClient();
// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your 
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('YOUR_CALLBACK_URI');
$client->setApplicationName("Latitude History Import");
$service = new 
apiLatitudeService($client);

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
  $_SESSION['access_token'] = $client->getAccessToken();
}

if (isset($_REQUEST['getHistory'])) {
  $optParam = array( "granularity"=>"best",
			"max-results"=>"1000");
  $results = array();
  $location = $service->location->listLocation($optParam);
  $temp = end($location["items"]);
  $last = $temp["timestampMs"] - 1;
  $optParam = array_merge($optParam,array("max-time"=>"$last"));
  $results = array_merge($results,$location["items"]);    
  while (count($location["items"]) == 1000) {
    $location = $service->location->listLocation($optParam);
    $temp = end($location["items"]);
    $last = $temp["timestampMs"] - 1;
    $optParam = array_merge($optParam,array("max-time"=>"$last"));
    $results = array_merge($results,$location["items"]);           	
  }  
  $_SESSION['History'] = $results;
}

if (isset($_REQUEST['sendHistory'])  && isset($_SESSION['History'])) {
  $results = $_SESSION['History'];
  $i = 0;
  while (count($results) > $i) {
    $result = $results[$i];
    $Location = new Location();
    $Location->setKind($result["kind"]);
    $Location->setTimestampMs($result["timestampMs"]);
    $Location->setLatitude($result["latitude"]);
    $Location->setLongitude($result["longitude"]);
    $Location->setAccuracy($result["accuracy"]);
    $Location->setSpeed($result["speed"]);
    $Location->setAltitude($result["altitude"]);
    $Location->setAltitudeAccuracy($result["altitudeAccuracy"]);
    $loc = $service->location->insert($Location);
    ++$i;
  }
  unset($_SESSION['History']);
}
?>
<!doctype html>
<html>
<head><link rel='stylesheet' href='style.css' /></head>
<body>
<header><h1>Google Latitude History Export/Import Tool</h1></header>
<div class="box">
  <?php
    if(isset($authUrl)) {
      if (isset($_SESSION['History'])) { $history = "destination"; } else { $history = "source"; }
      print "<a class='text' href='$authUrl'>Login to $history!</a>";
    } else {
      if (isset($_SESSION['History'])) { print "<a class='text' href='?sendHistory'>Send History</a>"; } else { print "<a class='text' href='?getHistory'>Get History</a>"; }
      print "<br><a class='text' href='?logout'>Logout</a><br>";
    }
  ?>
</div>
<pre class="help">
Instructions:

1) First click "Login to source!" which will ask you to authorize the application. Be sure you are logging into the SOURCE (where your latitude history is) account.
2) Click "Get History" which will grab all of your location history and store it as a PHP Session array.
3) Click "Logout".
4) Click "Login to destination!" which will ask you to authorize the application. Be sure you are logging into the DESTINATION (where your latitude history will be imported too) account.
5) Click "Send History" which will import everything that was grabbed earlier and store it into your new account.

Warning:

I am not responsible for data loss or corruption. Please use this at your own risk. The risk is very minimial but the code does not have much error checking so mileage will vary. Also, the Google API for inserting locations is done by the timestamp therefore if two locations exist with same timestamp (one in source and one in destination) then the destination location will be overwritten with the source location.

Data is never stored on the server however a $_SESSION variable holds all of your location history while you log out/in between accounts to insert it back in. PHP will become available soon if you wish to run on your own server.
</pre></body></html>