<?php

namespace OpenOrchestra\LogBundle\Model;

/**
 * Class LogInterface
 */
interface LogInterface
{
    const ENTITY_TYPE = 'log';

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $message
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param array $context
     */
    public function setContext(array $context);

    /**
     * @return array
     */
    public function getContext();

    /**
     * @param int $level
     */
    public function setLevel($level);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param string $levelName
     */
    public function setLevelName($levelName);

    /**
     * @return string
     */
    public function getLevelName();

    /**
     * @param string $channel
     */
    public function setChannel($channel);

    /**
     * @return string
     */
    public function getChannel();

    /**
     * @param string $dateTime
     */
    public function setDateTime($dateTime);

    /**
     * @return string
     */
    public function getDateTime();

    /**
     * @param array $extra
     */
    public function setExtra(array $extra);

    /**
     * @return array
     */
    public function getExtra();
}
