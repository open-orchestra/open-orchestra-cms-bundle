<?php

namespace OpenOrchestra\LogBundle\Document;

use OpenOrchestra\LogBundle\Model\LogInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Mapping\Annotations as ORCHESTRA;

/**
 * Class Log
 *
 * @ODM\Document(
 *   collection="log",
 *   repositoryClass="OpenOrchestra\LogBundle\Repository\LogRepository"
 * )
 */
class Log implements LogInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $message
     *
     * @ODM\Field(type="string")
     */
    protected $message;

    /**
     * @var array $context
     *
     * @ODM\Field(type="collection")
     */
    protected $context = array();

    /**
     * @var int $level
     *
     * @ODM\Field(type="int")
     */
    protected $level;

    /**
     * @var string $levelName
     *
     * @ODM\Field(type="string")
     */
    protected $levelName;

    /**
     * @var string $channel
     *
     * @ODM\Field(type="string")
     */
    protected $channel;

    /**
     * @var string $datetime
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="date_time")
     */
    protected $datetime;

    /**
     * @var array $extra
     *
     * @ODM\Field(type="collection")
     * @ORCHESTRA\Search(field="extra.user_ip",key="user_ip")
     * @ORCHESTRA\Search(field="extra.user_name",key="user_name")
     * @ORCHESTRA\Search(field="extra.site_name",key="site_name")
     */
    protected $extra;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getDateTime()
    {
        return $this->datetime;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }
}
