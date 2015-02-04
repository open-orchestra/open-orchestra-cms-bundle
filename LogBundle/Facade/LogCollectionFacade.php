<?php

namespace PHPOrchestra\LogBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;

/**
 * Class LogCollectionFacade
 */
class LogCollectionFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'logs';

    /**
     * @Serializer\Type("array<PHPOrchestra\LogBundle\Facade\LogFacade>")
     */
    public $logs = array();

    /**
     * @param FacadeInterface $log
     */
    public function addLog(FacadeInterface $log)
    {
        $this->logs[] = $log;
    }

    /**
     * @return mixed
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
