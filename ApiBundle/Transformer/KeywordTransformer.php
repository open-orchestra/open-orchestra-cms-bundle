<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\KeywordFacade;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class KeywordTransformer
 */
class KeywordTransformer extends AbstractTransformer
{
    /**
     * @param KeywordInterface $mixed
     *
     * @return KeywordFacade
     */
    public function transform($mixed)
    {
        $facade = new KeywordFacade();

        $facade->id = $mixed->getId();
        $facade->label = $mixed->getLabel();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_keyword_show',
            array('keywordId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_keyword_delete',
            array('keywordId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_keyword_form',
            array('keywordId' => $mixed->getId())
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
