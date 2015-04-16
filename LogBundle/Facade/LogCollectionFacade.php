<?php

namespace OpenOrchestra\LogBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\AbstractFacade;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
/**
 * Class LogCollectionFacade
 */
class LogCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'logs';

    /**
     * @Serializer\Type("array<OpenOrchestra\LogBundle\Facade\LogFacade>")
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
