<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\RedirectionCollectionFacade;

/**
 * Class RedirectionCollectionTransformer
 */
class RedirectionCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new RedirectionCollectionFacade();

        foreach ($mixed as $redirection) {
            $facade->addRedirection($this->getTransformer('redirection')->transform($redirection));
        }

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_redirection_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'php_orchestra_backoffice_redirection_new',
            array()
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'redirection_collection';
    }
}
