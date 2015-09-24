<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use OpenOrchestra\GroupBundle\Form\Type\OrchestraGroupType;
use Phake;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;

/**
 * Test OrchestraGroupTypeTest
 */
class OrchestraGroupTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraGroupType
     */
    protected $type;

    protected $groupClass = 'groupClass';
    protected $translationChoiceManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translationChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');

        $this->type = new OrchestraGroupType($this->groupClass, $this->translationChoiceManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('OpenOrchestra\BackofficeBundle\Form\Type\AbstractOrchestraGroupType', $this->type);
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->type);
    }

    /**
     * Test configure options
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $translationChoiceManager = $this->translationChoiceManager;

        $this->type->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'class' => $this->groupClass,
            'choice_label' => function (GroupInterface $choice) use ($translationChoiceManager) {
                return $translationChoiceManager->choose($choice->getLabels());
            },
        ));
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertSame('document', $this->type->getParent());
    }

    /**
     * Test name
     */
    public function testGetName()
    {
        $this->assertSame('orchestra_group', $this->type->getName());
    }
}
