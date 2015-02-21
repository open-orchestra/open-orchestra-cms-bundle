<?php

namespace OpenOrchestra\LogBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Transformer\AbstractTransformer;
use OpenOrchestra\LogBundle\Facade\LogCollectionFacade;

/**
 * Class LogCollectionTransformer
 */
class LogCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new LogCollectionFacade();

        foreach ($mixed as $log) {
            $facade->addLog($this->getTransformer('log')->transform($log));
        }

        return $facade;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'log_collection';
    }
}
