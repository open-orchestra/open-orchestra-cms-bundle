<?php

namespace OpenOrchestra\ApiBundle\Facade\Traits;

/**
 * Class BaseFacade
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
trait BaseFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $id;

    /**
     * @Serializer\XmlMap(inline=false, entry="link", keyAttribute="location")
     */
    protected $links = array();

    /**
     * @param array $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * @param string $name
     * @param string $link
     */
    public function addLink($name, $link)
    {
        $this->links[$name] = $link;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
}
