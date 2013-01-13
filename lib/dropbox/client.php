<?php
/**
 * An API client for Dropbox
 */
class DropboxClient
{
    protected $Session = null;

    /**
     * Common API URL
     * @var
     */
    protected $dropboxAPIURL = "https://api.dropbox.com/1";

    /**
     * Content-related API URL
     * @var
     */
    protected $dropboxContentAPIURL = "https://api-content.dropbox.com/1";

    /**
     * Constructor
     *
     * Initialize the client wit a valid session
     *
     * @param  object  $session
     * @return void
     */
    function __construct( DropboxSession $session) {
        $this->Session = $session;
        $this->accessType = $this->Session->getAccessType();
    }

    /**
     * Retrieve information from the user's account
     *
     * @return string
     */
    public function accountInfo() {
        $response = $this->Session->fetch("GET", $this->dropboxAPIURL, "/account/info");
        return $response["body"];
    }

    /**
     * Fetch metadata for a file or folder
     *
     * The path is relative to a root (ex /<root>/<path>) that can be 'sandbox' or 'dropbox'
     *
     * @param  string   $path             The path of the resource to fetch
     * @param  boolean  $list             Whether to list all contained files (applies only to folders)
     * @param  int      $fileLimit        Max items returned with the listing mode
     * @param  string   $hash             Hash value for a previous call
     * @param  string   $revision         Specific revision for the object
     * @param  bool     $includeDeleted   Whether to include deleted files and folders
     * @return string
     */
    public function metadata($path, $list = true, $fileLimit = 10000, $hash = null, $revision = null, $includeDeleted = false) {
        // Prepare argument list
        $args = array(
            "file_limit" => $fileLimit,
            "hash" => $hash,
            "list" => (int) $list,
            "include_deleted" => (int) $includeDeleted,
            "rev" => $revision
        );

        // Prepend the right access string to the desired path
        if ("dropbox" == $this->accessType) {
            $path = "dropbox" . $path;
        }
        else {
            $path = "sandbox" . $path;
        }

        // Execute
        $response = $this->Session->fetch("GET", $this->dropboxAPIURL, "/metadata/" . $path, $args);
        return $response["body"];
    }

    /**
     * Downloads a file from the user's Dropbox
     *
     * The path is relative to a root (ex /<root>/<path>) that can be 'sandbox' or 'dropbox'
     *
     * @param  string   $path             The path of the resource to fetch
     * @param  string   $outFile          The download path for the file
     * @param  string   $revision         Specific revision for the object
     * @return array
     */
    public function getFile($path, $outFile = null, $revision = null) {

        $args = array();
        if (!empty($revision)) {
            $args["rev"] = $revision;
        }

        // Prepend the right access string to the desired path
        if ("dropbox" == $this->accessType) {
            $path = "dropbox" . $path;
        }
        else {
            $path = "sandbox" . $path;
        }

        // Get the raw response body
        $response = $this->Session->fetch("GET", $this->dropboxContentAPIURL, "/files/" . $path, $args, true);

        if ($outFile != null) {
            if (file_put_contents($outFile, $response["body"]) === false) {
                throw new Exception("Unable to write file '$outfile'");
            }
        }

        return array(
            "name" => ($outFile) ? $outFile : basename($path),
            "mime" => $response["headers"]["content-type"],
            "meta" => json_decode($response["headers"]["x-dropbox-metadata"]),
            "data" => $response["body"]
        );
    }

    /**
     * Upload a file to the user's Dropbox
     *
     * The path is relative to a root (ex /<root>/<path>) that can be 'sandbox' or 'dropbox'
     *
     * @param  string   $file       The full path of the file to upload
     * @param  string   $path       The destination path (default = root)
     * @param  string   $name       Specifies a different name for the uploaded file
     * @param  boolean  $overwrite  Overwrite any existing file
     * @return array
     */
    public function putFile($file, $path = "/", $name = null, $overwrite = true) {
        // Check for file existence before
        if (!file_exists($file)) {
            throw new Exception("Local file '" . $file . "' does not exist");
        }

        // Dropbox has a 150MB limit upload for the API
        if (filesize($file) > 157286400) {
            throw new Exception("File exceeds 150MB upload limit");
        }

        $args = array(
            "overwrite" => (int) $overwrite,
            "inputfile" => $file
        );

        // Prepend the right access string to the desired path
        if ("dropbox" == $this->accessType) {
            $path = "dropbox" . $path;
        }
        else {
            $path = "sandbox" . $path;
        }

        // Determine the full path
        if (!empty($name)) {
            $path = dirname($path) . "/" . $name;
        }
        else {
            $path .= basename($file);
        }

        // Get the raw response body
        $response = $this->Session->fetch("PUT", $this->dropboxContentAPIURL, "/files_put/" . $path, $args);

        return $response["body"];
    }
}
