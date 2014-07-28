<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\NodeFacade;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelBundle\Model\NodeInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractTransformer
{
    /**
     * @param NodeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new NodeFacade();

        foreach ($mixed->getAreas() as $area) {
            $facade->addArea($this->getTransformer('area')->transform($area, $mixed));
        }

        return $facade;
    }

    /**
     * @param NodeFacade|FacadeInterface $facade
     * @param mixed|null                 $source
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (null === $source) {
            $source = new Node();
        }

        if (isset($facade->siteId)) {
            $source->setSiteId($facade->siteId);
        }

        if (isset($facade->deleted) && null !== $facade->deleted) {
            $source->setDeleted($facade->deleted);
        }

        if (isset($facade->templateId)) {
            $source->setTemplateId($facade->templateId);
        }

        if (isset($facade->name)) {
            $source->setName($facade->name);
        }

        if (isset($facade->nodeType)) {
            $source->setNodeType($facade->nodeType);
        }

        if (isset($facade->parentId)) {
            $source->setParentId($facade->parentId);
        }

        if (isset($facade->path)) {
            $source->setPath($facade->path);
        }

        if (isset($facade->alias)) {
            $source->setAlias($facade->alias);
        }

        if (isset($facade->language)) {
            $source->setLanguage($facade->language);
        }

        if (isset($facade->status)) {
            $source->setStatus($facade->status);
        }

        if (isset($facade->theme)) {
            $source->setTheme($facade->theme);
        }

        foreach ($facade->getAreas() as $area) {
            $source->addArea($this->getTransformer('area')->reverseTransform($area, null, $source));
        }

        foreach ($facade->getBlocks() as $block) {
            $blockArray = $this->getTransformer('block')->reverseTransform($block, $source);
            if (array_key_exists('block', $blockArray)) {
                $source->setBlock($blockArray['blockId'], $blockArray['block']);
            }
        }

        return $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node';
    }

}
