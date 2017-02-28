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

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return RedirectionInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $redirections = array();
        $redirectionsFacade = $facade->getRedirections();
        foreach ($redirectionsFacade as $redirectionFacade) {
            $redirection = $this->getTransformer('redirection')->reverseTransform($redirectionFacade);
            if (null !== $redirection) {
                $redirections[] = $redirection;
            }
        }

        return $redirections;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'redirection_collection';
    }
}
