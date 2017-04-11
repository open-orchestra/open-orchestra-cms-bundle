<?php
namespace OpenOrchestra\Backoffice\Tests\Reference\Strategies;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class AbstractReferenceStrategyTest
 */
abstract class AbstractReferenceStrategyTest extends AbstractBaseTestCase
{
    protected $strategy;

    /**
     * @param mixed   $entity
     * @param boolean $isSupported
     *
     * @dataProvider provideEntity
     */
    public function testSupport($entity, $isSupported)
    {
        $support = $this->strategy->support($entity);
        $this->assertSame($isSupported, $support);
    }

    /**
     * provide entity
     *
     * @return array
     */
    abstract public function provideEntity();

    /**
     * @param mixed  $entity
     * @apram string $entityId
     * @param array  $usedItems
     */
    abstract function testAddReferencesToEntity($entity, $entityId, array $usedItems);

    /**
     * @param mixed  $entity
     * @apram string $entityId
     * @param array  $usedItems
     */
    abstract function testRemoveReferencesToEntity($entity, $entityId, array $usedItems);

    /**
     * @param mixed  $entity
     * @param string $entityId
     * @param array  $usedItems
     * @param string $entityType
     */
    protected function checkAddReferencesToEntity($entity, $entityId, array $usedItems, $entityType, $itemRepository)
    {
        foreach ($usedItems as $itemId => $item) {
            Phake::when($itemRepository)->find($itemId)->thenReturn($item);
        }

        $this->strategy->addReferencesToEntity($entity);

        foreach ($usedItems as $itemId => $item) {
            Phake::verify($item)->addUseInEntity($entityId, $entityType);
        }
    }

    /**
     * @param mixed  $entity
     * @apram string $entityId
     * @param array  $usedItems
     * @param string $entityType
     */
    protected function checkRemoveReferencesToEntity($entity, $entityId, array $usedItems, $entityType, $itemRepository)
    {
        Phake::when($itemRepository)->findByUsedInEntity(Phake::anyParameters())->thenReturn($usedItems);

        $this->strategy->removeReferencesToEntity($entity);

        foreach ($usedItems as $item) {
            Phake::verify($item)->removeUseInEntity($entityId, $entityType);
        }
    }

    /**
     * Create a Phake Content
     *
     * @param  string $contentId
     * @param  array  $attributes
     *
     * @return Phake_IMock
     */
    protected function createPhakeContent($contentId = 'contentId', array $attributes = array())
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->getId()->thenReturn($contentId);
        Phake::when($content)->getAttributes()->thenReturn(new ArrayCollection($attributes));

        return $content;
    }

    /**
     * Create a Phake node
     *
     * @return Phake_IMock
     */
    protected function createPhakeNode($nodeId = 'nodeId')
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getId()->thenReturn($nodeId);

        return $node;
    }

    /**
     * Create a Phake node
     *
     * @return Phake_IMock
     */
    protected function createPhakeBlock($blockId = 'blockId')
    {
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getId()->thenReturn($blockId);

        return $block;
    }

    /**
     * Create a Phake Content Type
     *
     * @return Phake_IMock
     */
    protected function createPhakeContentType($contentTypeId = 'contentTypeId')
    {
        $contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType)->getContentTypeId()->thenReturn($contentTypeId);

        return $contentType;
    }

    /**
     * Create a Phake Keyword
     *
     * @param string $keywordId
     *
     * @return Phake_IMock
     */
    protected function createPhakeKeyword($keywordId = 'keywordId')
    {
        $keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($keyword)->getId()->thenReturn($keywordId);

        return $keyword;
    }

    /**
     * Create a Phake Content Attribute
     *
     * @param string $value
     * @param string $type
     *
     * @return Phake_IMock
     */
    protected function createPhakeContentAttribute($value = '', $type = '')
    {
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        Phake::when($attribute)->getValue()->thenReturn($value);
        Phake::when($attribute)->getType()->thenReturn($type);

        return $attribute;
    }
}
