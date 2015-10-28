<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\KeywordFacade;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;

/**
 * Class KeywordTransformer
 */
class KeywordTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param KeywordInterface $keyword
     *
     * @return KeywordFacade
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($keyword)
    {
        if (!$keyword instanceof KeywordInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new KeywordFacade();

        $facade->id = $keyword->getId();
        $facade->label = $keyword->getLabel();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_keyword_show',
            array('keywordId' => $keyword->getId())
        ));

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_KEYWORD)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_keyword_delete',
                array('keywordId' => $keyword->getId())
            ));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_KEYWORD)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_keyword_form',
                array('keywordId' => $keyword->getId())
            ));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'keyword';
    }
}
