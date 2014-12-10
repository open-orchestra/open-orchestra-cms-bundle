<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\KeywordFacade;
use PHPOrchestra\ModelBundle\Document\Role;

/**
 * Class KeywordTransformer
 */
class KeywordTransformer extends AbstractTransformer
{
    /**
     * @param Keyword $mixed
     *
     * @return KeywordFacade
     */
    public function transform($mixed)
    {
        $facade = new KeywordFacade();

        $facade->id = $mixed->getId();
        $facade->label = $mixed->getLabel();

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_keyword_show',
            array('keywordId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_keyword_delete',
            array('keywordId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_backoffice_keyword_form',
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
