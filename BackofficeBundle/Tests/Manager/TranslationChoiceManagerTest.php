<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class TranslationChoiceManagerTest
 */
class TranslationChoiceManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslationChoiceManager
     */
    protected $manager;

    protected $names;
    protected $object;
    protected $enName;
    protected $frName;
    protected $baseValue;
    protected $contextManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->baseValue = array('en' => 'English', 'fr' => 'Francais');

        $this->frName = new TranslatedValue();
        $this->frName->setLanguage('fr');
        $this->frName->setValue($this->baseValue['fr']);

        $this->enName = new TranslatedValue();
        $this->enName->setLanguage('en');
        $this->enName->setValue($this->baseValue['en']);

        $this->names = new ArrayCollection();
        $this->names->add($this->enName);
        $this->names->add($this->frName);

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');

        $this->manager = new TranslationChoiceManager($this->contextManager);
    }

    /**
     * @param string $lang
     *
     * @dataProvider provideLang
     */
    public function testChooseMethod($lang)
    {
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn($lang);

        $returnedValue = $this->manager->choose($this->names);

        $this->assertSame($this->baseValue[$lang], $returnedValue);
    }

    /**
     * @return array
     */
    public function provideLang()
    {
        return array(
            array('en'),
            array('fr'),
        );
    }

    /**
     * Test with no translations
     */
    public function testChooseWithEmptyCollection()
    {
        $this->assertSame('no translation', $this->manager->choose(new ArrayCollection()));
    }

    /**
     * Test with new language
     */
    public function testChooseWithNotIncludedLanguage()
    {
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn('de');

        $this->assertSame($this->baseValue['en'], $this->manager->choose($this->names));
    }
}
