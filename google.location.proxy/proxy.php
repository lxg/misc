<?php
/*
    (C) Alex Günsche 2015. Published under the MIT license.
    See the README.md file to understand what this script does and how it is to be used.
*/


# define the desired location of the log file
$logFile = '/tmp/google.location.log';


### ok, let's rock

$GoogleLocation = new GoogleLocation();
$GoogleLocation->callGoogle();
$GoogleLocation->logData($logFile);

// output to Firefox
echo $GoogleLocation->getGoogleResult();

class GoogleLocation
{
    private $requestPostData;

    private $requestHeaders = [];

    private $apiKey;

    private $googleUrl = "https://www.googleapis.com/geolocation/v1/geolocate?key=%s";

    private $googleResult = '';

    public function __construct()
    {
        // load API key

        if (!isset($_GET['key']))
            throw new \Exception("ERROR: No key was passed.");

        $this->apiKey = $_GET['key'];

        // load headers

        foreach ($_SERVER as $key => $value)
        {
            if (stripos($key, "HTTP_") === 0 && stripos($key, "Host") === false)
            {
                $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                $this->requestHeaders[] = "$key: $value";
            }
        }

        // load POST data
        $this->requestPostData = file_get_contents('php://input');

        if (!$this->requestPostData)
            throw new \Exception("ERROR: No POST data was submitted.");
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getRequestHttpHeaders()
    {
        return $this->requestHeaders;
    }

    public function getRequestPostData()
    {
        return $this->requestPostData;
    }

    public function getGoogleResult()
    {
        return $this->googleResult;
    }

    public function logData($logFile, $resetFile = false)
    {
        // prettify the request data
        $requestPostData = json_encode(json_decode($this->requestPostData), JSON_PRETTY_PRINT);

        file_put_contents($logFile, sprintf("----------------- %s -----------------\n", date('Y-m-d H:i:s')), $resetFile ? 0 : FILE_APPEND);
        file_put_contents($logFile, "Request: " . print_r($requestPostData, true) . "\n", FILE_APPEND);
        file_put_contents($logFile, "Response: " . print_r($this->googleResult, true) . "\n\n\n", FILE_APPEND);
    }

    public function callGoogle()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, sprintf($this->googleUrl, $this->apiKey));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->requestHeaders);
        curl_setopt($curl, CURLOPT_VERBOSE, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->requestPostData);

        $response = curl_exec($curl);

        if (curl_errno($curl))
            throw new \Exception(sprintf("Connection error %s: %s", curl_errno($curl), curl_error($curl)));

        curl_close($curl);

        $this->googleResult = gzdecode($response);
    }
}



