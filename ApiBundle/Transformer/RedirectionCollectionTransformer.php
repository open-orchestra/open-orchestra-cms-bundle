<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\RedirectionCollectionFacade;

/**
 * Class RedirectionCollectionTransformer
 */
class RedirectionCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $redirectionCollection
     *
     * @return FacadeInterface
     */
    public function transform($redirectionCollection)
    {
        $facade = new RedirectionCollectionFacade();

        foreach ($redirectionCollection as $redirection) {
            $facade->addRedirection($this->getTransformer('redirection')->transform($redirection));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_redirection_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_redirection_new',
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
