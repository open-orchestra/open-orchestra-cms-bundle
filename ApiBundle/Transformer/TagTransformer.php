<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\TagFacade;
use PHPOrchestra\ModelBundle\Document\Role;

/**
 * Class TagTransformer
 */
class TagTransformer extends AbstractTransformer
{
    /**
     * @param Tag $mixed
     *
     * @return TagFacade
     */
    public function transform($mixed)
    {
        $facade = new TagFacade();

        $facade->label = $mixed->getLabel();

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_tag_show',
            array('tagId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_tag_delete',
            array('tagId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_backoffice_tag_form',
            array('tagId' => $mixed->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tag';
    }
}
