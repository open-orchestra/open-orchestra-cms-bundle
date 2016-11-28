<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class KeywordCollectionTransformer
 */
class KeywordCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $keywordCollection
     *
     * @return FacadeInterface
     */
    public function transform($keywordCollection)
    {
        $facade = $this->newFacade();

        foreach ($keywordCollection as $keyword) {
            $facade->addKeyword($this->getTransformer('keyword')->transform($keyword));
        }

        $facade->addRight('can_create', $this->authorizationChecker->isGranted('ROLE_KEYWORD_ACCESS'));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'keyword_collection';
    }
}
