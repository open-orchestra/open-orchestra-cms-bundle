<?php

namespace PHPOrchestra\CMSBundle\Form\DataTransformer;

use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockManager;
use PHPOrchestra\CMSBundle\Document\DocumentManager;
use PHPOrchestra\CMSBundle\Model\Node;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class NodeTypeTransformer
 */
class NodeTypeTransformer implements DataTransformerInterface
{
    const BLOCK_GENERATE = 'generate';
    const BLOCK_LOAD = 'load';
    const JSON_AREA_TAG = 'areas';
    const PHP_AREA_TAG = 'subAreas';
    const CLASSES_TAG = 'classes';

    protected $documentManager;
    protected $blockManager;

    /**
     * @param DocumentManager     $documentManager
     * @param DisplayBlockManager $blockManager
     */
    public function __construct(DocumentManager $documentManager, DisplayBlockManager $blockManager)
    {
        $this->documentManager = $documentManager;
        $this->blockManager = $blockManager;
    }

    /**
     * Transforms a node
     *
     * @param Node $node
     *
     * @return Node
     */
    public function transform($node)
    {
        if(isset($node)){
            $areas = $node->getAreas();
            $blocks = $node->getBlocks()->getSaved();
            if (isset($areas)) {
                $areasArray = array(self::PHP_AREA_TAG => $areas);
                $areas = $this->recTransform($areasArray, $blocks);
            }
            $node->setAreas(json_encode($areas));
        }

        return $node;
    }

    /**
     * Transforms a json string to a node.
     *
     * @param mixed $node
     *
     * @return Node
     */
    public function reverseTransform($node)
    {
        if(isset($node)){
            $areas = json_decode($node->getAreas(), true);
            $node->removeBlocks($node->getBlocks()->getSaved());
            if (is_array($areas)) {
                $areas = $this->reverseRecTransform($areas, $node);
                $node->setAreas($areas[self::PHP_AREA_TAG]);
            }
        }
        return $node;
    }

    /**
     * @param array $values
     * @param array $blocks
     *
     * @return mixed
     */
    protected function recTransform($values, $blocks)
    {
        foreach($values as $key => &$value){
            if($key === 'blocks'){
                foreach($value as &$block){
                    if(array_key_exists('nodeId', $block) && array_key_exists('blockId', $block) && array_key_exists($block['blockId'], $blocks) && $block['nodeId'] === 0){
                        $blockRef = $blocks[$block['blockId']];
                        unset($block['blockId']);
                        unset($block['nodeId']);
                        $block['method'] = self::BLOCK_GENERATE;
                        $block['component'] = $blockRef->getComponent();
                        $attributs = $blockRef->getAttributes();
                        $attributs = array_combine(array_map(function($value) { return 'attributs_'.$value; }, array_keys($attributs)), array_values($attributs));
                        $block = array_merge($block, $attributs);
                    }
                    else{
                        $block['method'] = self::BLOCK_LOAD;
                        $node = $this->documentManager->getDocumentById('Node', $block['nodeId']);
                        $blocks = $node->getBlocks()->all();
                        $blockRef = $blocks[$block['blockId']];
                    }
                    $block['ui-model']['label'] = $block['component'];
                    $response = $this->blockManager->showBack($blockRef)->getContent();
                    $block['ui-model']['html'] = $response;
                }
                if(count($value) == 0){
                    unset($values['blocks']);
                }
            }
            if($key == self::PHP_AREA_TAG){
                foreach($value as &$area){
                    $areaId = $area['areaId'];
                    $area = $this->recTransform($area, $blocks);
                    $area[self::CLASSES_TAG] = implode(',', $area[self::CLASSES_TAG]);
                    $area['ui-model'] = array('label' => $areaId);
                }
                if(count($value) > 0){
                    $values[self::JSON_AREA_TAG] = $value;
                }
                unset($values[self::PHP_AREA_TAG]);
            }
        }
        return $values;
    }

    /**
     * @param array $values
     * @param Node $node
     *
     * @return mixed
     */
    public function reverseRecTransform($values, &$node)
    {
        foreach($values as $key => &$value){
            if($key === 'blocks'){
                foreach($value as &$block){
                    if(array_key_exists('method', $block) && $block['method'] === 'generate'){
                        $component = $block['component'];
                        unset($block['method']);
                        unset($block['component']);
                        unset($block['_token']);
                        unset($block['ui-model']);
                        $attributs = $block;
                        $attributs = array_combine(array_map(function($value) { return preg_replace('/^attributs_/', '', $value); }, array_keys($attributs)), array_values($attributs));
                        $blockDoc = $this->documentManager->createDocument('Block')
                            ->setComponent($component)
                            ->setAttributes($attributs);
                        $block = array('nodeId' => 0, 'blockId' => $node->getBlocks()->count());
                        $node->addBlocks($blockDoc);
                    }
                    elseif(array_key_exists('nodeId', $block) && array_key_exists('blockId', $block)){
                        $block = array('nodeId' => $block['nodeId'], 'blockId' => $block['blockId']);
                    }
                }
            }
            if($key == self::JSON_AREA_TAG){
                foreach($value as &$area){
                    $area = $this->reverseRecTransform($area, $node);
                    $area[self::CLASSES_TAG] = explode(',', $area[self::CLASSES_TAG]);
                    unset($area['ui-model']);
                }
                $values[self::PHP_AREA_TAG] = $value;
                unset($values[self::JSON_AREA_TAG]);
            }
        }

        return $values;
    }
}
