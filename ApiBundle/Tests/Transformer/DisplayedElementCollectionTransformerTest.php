<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\DisplayedElementCollectionTransformer;

/**
 * Class DisplayedElementCollectionTransformerTest
 */
class DisplayedElementCollectionTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DisplayedElementCollectionTransformer
     */
    protected $displayedElementCollectionTransformer;
    protected $translator;

    /**
     * Set up the test
     *
     * @dataProvider getChangeStatus
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->displayedElementCollectionTransformer = new DisplayedElementCollectionTransformer($this->translator);
    }

    /**
     * @param string $entityType
     * @param array  $displayedElements
     * @param array  $expectedResult
     *
     * @dataProvider getTranslatedDisplayedElements
     */
    public function testTransform($entityType, $displayedElements, $expectedResults)
    {
        foreach($expectedResults as $expectedResult) {
            Phake::when($this->translator)->trans($expectedResult)->thenReturn($expectedResult);
        }

        $facade = $this->displayedElementCollectionTransformer->transform($displayedElements, $entityType);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\DisplayedElementCollectionFacade', $facade);
        $this->assertEquals($expectedResults, $facade->displayedElements);
    }

    /**
     * @return array
     */
    public function getTranslatedDisplayedElements()
    {
        return array(
            array('fakeEntityType', array('fake_column'), array('open_orchestra_backoffice.table.fake_entity_type.fake_column')),
            array('fake_entity_type', array('fake_column'), array('open_orchestra_backoffice.table.fake_entity_type.fake_column')),
            array('fake', array('fake_column'), array('open_orchestra_backoffice.table.fake.fake_column')),
        );
    }

}
