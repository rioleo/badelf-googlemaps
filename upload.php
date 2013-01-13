<?php
require_once "bootstrap.php";

if (!isset($access_token)) {
    header("Location: authorize.php");
    exit;
}

try {
    // Start a new Dropbox session
    // The access token should be passed
    // The session should verify if the token is valid and throw an exception
    $session = new DropboxSession(
        $config["dropbox"]["app_key"], 
        $config["dropbox"]["app_secret"], 
        $config["dropbox"]["access_type"], 
        $access_token
    );

    $client = new DropboxClient($session);

    $src = $config["app"]["datadir"] . "/test.png";
    $dest = "/";

    // Upload a file
    if ($response = $client->putFile($src, $dest)) {
        echo "<p>File successfully uploaded!</p>";
        echo "<pre>" . print_r($response, true) . "</pre>";
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
