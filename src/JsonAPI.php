<?php

namespace Consolidation\JsonAPI;

/**
 * JSON API CLI commands
 */
class JsonAPI
{
    public function get($url)
    {
        return $this->do($url);
    }

    public function post($url, $data)
    {
        return $this->do($url, 'POST', $data);
    }

    public function hasToken()
    {
        return false;
    }

    public function token()
    {
        return '';
    }

    protected function do($url, $method = 'GET', $data = [])
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
