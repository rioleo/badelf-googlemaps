<?php
require_once "bootstrap.php";

if (!isset($access_token)) {
    header("Location: ./authorize.php");
    exit;
}
try {
    // Start a new Dropbox session
    $session = new DropboxSession(
        $config["dropbox"]["app_key"], 
        $config["dropbox"]["app_secret"], 
        $config["dropbox"]["access_type"], 
        $access_token
    );

    $client = new DropboxClient($session);

    // Retrieve account info
    if ($info = $client->accountInfo()) {
        echo "<p>Account Details for User <strong>" . $info["display_name"] . "</strong> - ";
        echo '<a href="list.php">View Files</a></p>';
        echo "<pre>" . print_r($info, true)  . "</pre>";
    }
}
catch (Exception $e) {
    echo "<strong>ERROR (" . $e->getCode() . ")</strong>: " . $e->getMessage();
    if ($e->getCode() == 401) {
        // Remove auth file
        unlink($config["app"]["authfile"]);
        // Re auth
        echo '<p><a href="authorize.php">Click Here to re-authenticate</a></p>';
    }
}
