<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\TagCollectionFacade;

/**
 * Class TagCollectionTransformer
 */
class TagCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new TagCollectionFacade();

        foreach ($mixed as $tag) {
            $facade->addTag($this->getTransformer('tag')->transform($tag));
        }

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_tag_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'php_orchestra_backoffice_tag_new',
            array()
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tag_collection';
    }
}
