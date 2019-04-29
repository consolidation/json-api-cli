<?php

namespace Consolidation\JsonAPI;

/**
 * JSON API CLI commands
 */
class JsonAPI
{
    protected $token;

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
        return isset($this->token);
    }

    public function token()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    protected function do($url, $method = 'GET', $data = [])
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'User-Agent' => 'consolidation/json-api-cli',
        ];

        // See https://www.drupal.org/project/simple_oauth
        if ($this->hasToken()) {
            $headers['Authorization'] = "Bearer " . $this->token();;
        }

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
