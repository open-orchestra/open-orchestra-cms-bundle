<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeHttpException;
use OpenOrchestra\ApiBundle\Facade\KeywordFacade;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class KeywordTransformer
 */
class KeywordTransformer extends AbstractTransformer
{
    /**
     * @param KeywordInterface $keyword
     *
     * @return KeywordFacade
     *
     * @throws TransformerParameterTypeHttpException
     */
    public function transform($keyword)
    {
        if (!$keyword instanceof KeywordInterface) {
            throw new TransformerParameterTypeHttpException();
        }

        $facade = new KeywordFacade();

        $facade->id = $keyword->getId();
        $facade->label = $keyword->getLabel();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_keyword_show',
            array('keywordId' => $keyword->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_keyword_delete',
            array('keywordId' => $keyword->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_keyword_form',
            array('keywordId' => $keyword->getId())
        ));

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
