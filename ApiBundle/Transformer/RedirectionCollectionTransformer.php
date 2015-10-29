<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ApiBundle\Facade\RedirectionCollectionFacade;

/**
 * Class RedirectionCollectionTransformer
 */
class RedirectionCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
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

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_REDIRECTION)) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_redirection_new',
                array()
            ));
        }

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
