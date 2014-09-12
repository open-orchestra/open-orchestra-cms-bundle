<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\StatusCollectionFacade;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class StatusCollectionTransformer
 */
class StatusCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface|StatusCollectionFacade
     */
    public function transform($mixed)
    {
        $facade = new StatusCollectionFacade();

        foreach ($mixed as $status) {
            $facade->addStatus($this->getTransformer('status')->transform($status));
        }

        $facade->addLink('_self_add', $this->getRouter()->generate(
            'php_orchestra_backoffice_status_new',
            array(),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'status_collection';
    }

}
