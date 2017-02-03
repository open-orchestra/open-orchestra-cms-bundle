<?php

namespace OpenOrchestra\Backoffice\Manager;

/**
 * Class ClientConfigurationManager
 */
class ClientConfigurationManager
{
    protected $clientConfiguration = array();

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addClientConfiguration($key, $value)
    {
        $this->clientConfiguration[$key] = $value;
    }

    /**
     * @return array
     */
    public function getClientConfiguration()
    {
        return $this->clientConfiguration;
    }
}
