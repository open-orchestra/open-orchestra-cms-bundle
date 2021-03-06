<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;

/**
 * Class RedirectionCollectionTransformer
 */
class RedirectionCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $redirectionCollection
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($redirectionCollection, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($redirectionCollection as $redirection) {
            $facade->addRedirection($this->getContext()->transform('redirection', $redirection));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return RedirectionInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        $redirections = array();
        $redirectionsFacade = $facade->getRedirections();
        foreach ($redirectionsFacade as $redirectionFacade) {
            $redirection = $this->getContext()->reverseTransform('redirection', $redirectionFacade);
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
