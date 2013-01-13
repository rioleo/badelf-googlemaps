<?php
// Prevent calling this script directly
if ($_SERVER["SCRIPT_FILENAME"] == __FILE__) {
    exit("Access denied!");
}

// app settings
$config = array();



$config["dropbox"]["app_key"] = "APPKEYHERE";
$config["dropbox"]["app_secret"] = "APPSECRETHERE";


// ACCESS_TYPE should be "dropbox" or "app_folder"
// No need to modify below here
$config["dropbox"]["access_type"] = "app_folder";

$config["app"]["root"] = ((!empty($_SERVER["HTTPS"])) ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . "/dev/gps/";
$config["app"]["datadir"] = dirname(__FILE__) . "/data";
$config["app"]["authfile"] = $config["app"]["datadir"] . "/auth.php";

// turn on error reporting for development
error_reporting(E_ALL|E_STRICT);
ini_set("display_errors", true);

// environment check
if (!is_dir($config["app"]["datadir"]) || !is_writable($config["app"]["datadir"])) {
    exit("The data directory is not writeable!");
}
if (file_exists($config["app"]["authfile"]) && !is_writable($config["app"]["authfile"])) {
    exit("The auth storage file is not writeable!");
}

// Load libraries and start a new session
require_once "lib/dropbox/rest.php";
require_once "lib/dropbox/session.php";
require_once "lib/dropbox/client.php";

session_start();

// Search for a previously obtained access token
$access_token = null;
if (file_exists($config["app"]["authfile"])) {
    include_once $config["app"]["authfile"];
}
