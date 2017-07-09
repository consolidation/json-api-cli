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
            return $base_url . '/' . $url;
        }
        return $url;
    }
}
