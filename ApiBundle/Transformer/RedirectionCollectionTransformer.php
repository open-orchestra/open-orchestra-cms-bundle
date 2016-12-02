<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;

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
        $facade = $this->newFacade();

        foreach ($redirectionCollection as $redirection) {
            $facade->addRedirection($this->getTransformer('redirection')->transform($redirection));
        }

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, RedirectionInterface::ENTITY_TYPE)) {
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
