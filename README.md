Export BadElf GPS to Google Maps
================================

This is a relatively easy to implement hack that lets users
upload logged tracks on BadElf GPS Pro to a server of their
choice, allowing friends and family to track a traveler's 
waypoints. It uses an authorized Dropbox app process to 
transfer the KML files that Google Maps then renders. 

Setup
-------------------------
The overall process is something like this:

* Upload all files to server
* Register a new API key with Dropbox
* Modify bootstrap.php with app key and secret
* Authorize app via authorize.php
* Modify line 23 of map.php (it has to be an absolute URL)
* Done!

Usage
-------------------------
* Log GPS path using BadElf
* Export to iOS device using Bluetooth
* Save trip, Choose Export KML and "Open with" Dropbox
* Select app folder (under Apps/appname)
* Go to list.php
* Transfer a log
* Logs visible on map.php

Troubleshooting
-------------------------
* The spikes are a known issue (http://code.google.com/p/gmaps-api-issues/issues/detail?id=4776)
* If the KML files don't load, your server may not be able to deliver KML files with the right headers. Check your host for details.


The codebase is based on Access Dropbox Using PHP by Vito
Tardia.