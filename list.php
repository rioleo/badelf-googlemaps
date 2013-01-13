<p>Click on a file to transfer it to your server.</p>

<?php
require_once "bootstrap.php";

if (!isset($access_token)) {
    header("Location: authorize.php");
    exit;
}

try {
    // Start a new Dropbox session
    // The access token should be defined
    // The session should verify if the token is valid and throw an exception
    $session = new DropboxSession(
        $config["dropbox"]["app_key"], 
        $config["dropbox"]["app_secret"], 
        $config["dropbox"]["access_type"], 
        $access_token
    );
    $client = new DropboxClient($session);

    $path = (!empty($_GET["path"])) ? $_GET["path"] : "/";

    // List contents of home directory
    if ($home = $client->metadata($path)) {
        
    foreach ($home["contents"] as $value) {
    	echo "<li><a href='download.php?path=".$value["path"]."'>".$value["path"]."</a></li>";	
    }
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
