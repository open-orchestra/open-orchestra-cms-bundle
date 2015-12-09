<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
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

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_keyword_list',
            array()
        ));

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_KEYWORD)) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_keyword_new',
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
        return 'keyword_collection';
    }
}
