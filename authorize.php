<?php
require_once "bootstrap.php";

if (isset($access_token)) {
    header("Location: ./");
    exit;
}

try {
    // Start a new Dropbox session
    $session = new DropboxSession(
        $config["dropbox"]["app_key"],
        $config["dropbox"]["app_secret"],
        $config["dropbox"]["access_type"]
    );

    // The user is redirected here by Dropbox after the authorization screen
    if (!empty($_GET["oauth_token"]) && !empty($_GET["uid"])) {
        $uid = $_GET["uid"];
        $token = array(
            "oauth_token" => $_GET["oauth_token"],
            "oauth_token_secret" => ""
        );

        if (!empty($_SESSION["request_token"])) {
            $token["oauth_token_secret"] = $_SESSION["request_token"]["oauth_token_secret"];
        }

        /**
         * The access token is all you'll need for all future API requests on
         * behalf of this user, so you should store it away for safe-keeping 
         * (even though we don't for this article). By storing the access
         * token, you won't need to go through the authorization process again
         * unless the user revokes access via the Dropbox website.
         */
        if ($access_token = $session->obtainAccessToken($token)) {
            parse_str($access_token, $token);
            $access_token = $token;
            unset($token);

            // The output ov var_export is similar to:
            // array("oauth_token_secret" => "aaaa", "oauth_token" => "bbbb", "uid" => "123456")
            $data = '<?php $access_token = ' . var_export($access_token, true) . ";";
            if (file_put_contents($config["app"]["authfile"], $data) === false) {
                throw new Exception("Unable save access token");
            }

            // Authorized, redirect to index
            header("Location: index.php");
            exit;
        }
        // The access token should be stored somewhere to be reused until
        // it expires or is revoked by the user
    }
    else {
        // We must start a new authorization cycle
        if ($request_token = $session->obtainRequestToken()) {
            // The request token must be subdivided in the two components 
            // oauth_token_secret and oauth_token and kept in the session
            // because is needed in the next step
            parse_str($request_token, $token);
            $_SESSION["request_token"] = $token;

            $url = $session->buildAuthorizeURL(
                $token, 
                $config["app"]["root"] . basename($_SERVER["SCRIPT_NAME"]),
                "en-US");

            // Display or redirect to auth URL
            echo '<p>Please visit <a href="' . $url . '">Dropbox</a> and authorize this application.</p>';
            exit;
        }
        else {
            throw new Exception("Unable to get request token");
        }
    }
}
catch (Exception $e) {
    echo $e->getMessage();
}
