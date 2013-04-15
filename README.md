Latitude-Export-Import
======================

Migrates Google Latitude history from one account to another.

Requirements
------------

+ You must have a web server with PHP installed
+ You must have a Google API code https://code.google.com/apis/console/
+ You must download a copy of the PHP API available from http://code.google.com/p/google-api-php-client/
+ You must edit the PHP with your API codes once obtained from above

Prerequisites
-------------

Your destination must have latitude history enabled or none of the data will stay on the account.

Instructions
------------

1. First click "Login to source!" which will ask you to authorize the application. Be sure you are logging into the SOURCE (where your latitude history is) account.
2. Click "Get History" which will grab all of your location history and store it as a PHP Session array.
3. Click "Logout".
4. Click "Login to destination!" which will ask you to authorize the application. Be sure you are logging into the DESTINATION (where your latitude history will be imported too) account.
5. Click "Send History" which will import everything that was grabbed earlier and store it into your new account.

Warning
-------

I am not responsible for data loss or corruption. Please use this at your own risk. The risk is very minimial but the code does not have much error checking so mileage will vary. Also, the Google API for inserting locations is done by the timestamp therefore if two locations exist with same timestamp (one in source and one in destination) then the destination location will be overwritten with the source location.

Data is never stored on the server however a $_SESSION variable holds all of your location history while you log out/in between accounts to insert it back in. PHP will become available soon if you wish to run on your own server.

The duration of sending your history may take up to 30 minutes or more depending on how many locations you have. For 12,000 locations it took about 30 mins. Please be patient.

[![githalytics.com alpha](https://cruel-carlota.pagodabox.com/4993921353734cef5a34e19a5def9944 "githalytics.com")](http://githalytics.com/salbahra/Latitude-Export-Import)
