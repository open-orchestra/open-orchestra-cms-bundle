<?php

namespace OpenOrchestra\ModelLogBundle\Document;

use OpenOrchestra\LogBundle\Model\LogInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Log
 *
 * @ODM\Document(
 *   collection="log",
 *   repositoryClass="OpenOrchestra\ModelLogBundle\Repository\LogRepository"
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
     */
    protected $datetime;

    /**
     * @var array $extra
     *
     * @ODM\Field(type="hash")
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
     * @param string $message
     */
    public function setMessage($message)
    {
        if (is_string($message)) {
            $this->message = $message;
        }
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        if (is_integer($level)) {
            $this->level = $level;
        }
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param string $levelName
     */
    public function setLevelName($levelName)
    {
        if (is_string($levelName)) {
            $this->levelName = $levelName;
        }
    }

    /**
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @param string $channel
     */
    public function setChannel($channel)
    {
        if (is_string($channel)) {
            $this->channel = $channel;
        }
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $dateTime
     */
    public function SetDateTime($dateTime)
    {
        if (is_string($dateTime)) {
            $this->datetime = $dateTime;
        }
    }

    /**
     * @return string
     */
    public function getDateTime()
    {
        return $this->datetime;
    }

    /**
     * @param array $extra
     */
    public function setExtra(array $extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }
}
