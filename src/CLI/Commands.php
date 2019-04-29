<?php

namespace Consolidation\JsonAPI\CLI;

use Consolidation\JsonAPI\JsonAPI;

/**
 * JSON API CLI commands
 */
class Commands extends \Robo\Tasks
{
    /**
     * @command get
     */
    public function get($url, $options = ['format' => 'yaml'])
    {
        $url = $this->expandURL($url, getenv('BASE_URL'));
        $api = new JsonAPI();
        $api->setToken(getenv('OAUTH_TOKEN'));
        return $api->get($url);
    }

    protected function expandURL($url, $base_url)
    {
        if ((strstr($url, '://') === false) && !empty($base_url)) {
            return rtrim($base_url, '/') . '/' . ltrim($url, '/');
        }
        return $url;
    }

    /**
     * @command auth
     */
    public function auth()
    {
        $url = $this->expandURL('/oauth/token', getenv('BASE_URL'));
        $api = new JsonAPI();

        // Works:
        // curl -L -i -k -X POST -d
        //    client_id=3c3d8c6f-e025-484f-98de-bfd46b237a9a&
        //    client_secret=secret&
        //    grant_type=client_credentials&
        //    client_id=3c3d8c6f-e025-484f-98de-bfd46b237a9a&
        //    client_secret=secret"
        //    https://live-updatinator.pantheonsite.io/oauth/token
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => '3c3d8c6f-e025-484f-98de-bfd46b237a9a',
            'client_secret' => 'secret',
            // 'scope' => 'client_developer', // space-delimited scopes
        ];

        return $this->guzzle($url, $data);
    }

    protected function guzzle($url, $data = [])
    {
        $headers = [
            'User-Agent' => 'consolidation/json-api-cli',
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ];

        $method = 'GET';
        $guzzleParams = [
            'headers' => $headers,
        ];
        if (!empty($data)) {
            $method = 'POST';
            $guzzleParams['form_params'] = $data;
        }

        print "parameters:\n";
        var_export($guzzleParams);
        print "\n";

        $client = new \GuzzleHttp\Client();
        print "about to request...\n";
        try
        {
            $res = $client->request($method, $url, $guzzleParams);
        }
        catch (\Exception $e)
        {
            print "Exception:\n" . $e->getMessage() . "\n";
            return;
        }
        print "returned from request\n";
        $rawResult = $res->getBody()->getContents();
        print "raw result:\n";
        var_export($rawResult);
        print "\n";
        $resultData = json_decode($res->getBody(), true);
        $httpCode = $res->getStatusCode();

        print "\nhttp code is: $httpCode\n";
        print "result data:\n";
        var_export($resultData);
        print "\n";

        $errors = [];
        if (isset($resultData['errors'])) {
            foreach ($resultData['errors'] as $error) {
                $errors[] = $error['message'];
            }
        }
        if ($httpCode && ($httpCode >= 300)) {
            $errors[] = "Http status code: $httpCode";
        }

        $message = isset($resultData['message']) ? "{$resultData['message']}." : '';

        if (!empty($message) || !empty($errors)) {
            throw new \Exception("Error: $message [status: $httpCode]");
        }

        return $resultData;
    }

}
