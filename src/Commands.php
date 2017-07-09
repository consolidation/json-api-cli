<?php

namespace Consolidation\JsonAPI\CLI;

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

        return $this->jsonAPI($url);
    }

    protected function expandURL($url, $base_url)
    {
        if ((strstr($url, '://') === false) && !empty($base_url)) {
            return $base_url . '/' . $url;
        }
        return $url;
    }

    protected function hasToken()
    {
        return false;
    }

    protected function token()
    {
        return '';
    }

    protected function jsonAPI($url, $data = [])
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'User-Agent' => 'consolidation/json-api-cli',
        ];

        // TODO: Fix this up. This is how
        // oauth tokens are passed to GitHub. JSON API may be different.
        if ($this->hasToken()) {
            $headers['Authorization'] = "token " . $this->token();;
        }

        $method = 'GET';
        $guzzleParams = [
            'headers' => $headers,
        ];
        if (!empty($data)) {
            $method = 'POST';
            $guzzleParams['json'] = $data;
        }

        $client = new \GuzzleHttp\Client();
        $res = $client->request($method, $url, $guzzleParams);
        $resultData = json_decode($res->getBody(), true);
        $httpCode = $res->getStatusCode();

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
