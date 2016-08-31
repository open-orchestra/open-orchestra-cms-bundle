<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\Form\Type\GroupDocumentType;
use Phake;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Test GroupDocumentTypeTest
 */
class GroupDocumentTypeTest extends AbstractBaseTestCase
{
    /**
     * @var GroupDocumentType
     */
    protected $type;

    protected $groupClass = 'groupClass';
    protected $multiLanguageChoiceManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->multiLanguageChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\MultiLanguagesChoiceManagerInterface');

        $this->type = new GroupDocumentType($this->groupClass, $this->multiLanguageChoiceManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->type);
    }

    /**
     * Test configure options
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $multiLanguageChoiceManager = $this->multiLanguageChoiceManager;

        $this->type->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'class' => $this->groupClass,
            'choice_label' => function (GroupInterface $choice) use ($multiLanguageChoiceManager) {
                return $multiLanguageChoiceManager->choose($choice->getLabels());
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
        $this->assertSame('oo_group_choice', $this->type->getName());
    }
}
