<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\ModelBundle\Document\TranslatedValue;
use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;

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
    protected $contextManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->frName = new TranslatedValue();
        $this->frName->setLanguage('fr');

        $this->enName = new TranslatedValue();
        $this->enName->setLanguage('en');

        $this->names = new ArrayCollection();
        $this->names->add($this->enName);
        $this->names->add($this->frName);

        $this->contextManager = Phake::mock('PHPOrchestra\Backoffice\Context\ContextManager');
    }

    /**
     * @param string $lang
     * @param array  $baseValue
     *
     * @dataProvider provideLangAndTranslation
     */
    public function testChooseMethod($lang, array $baseValue)
    {
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn($lang);
        $this->frName->setValue($baseValue['fr']);
        $this->enName->setValue($baseValue['en']);

        $this->manager = new TranslationChoiceManager($this->contextManager);
        $returnedValue = $this->manager->choose($this->names);

        $this->assertSame($returnedValue, $baseValue[$lang]);
    }

    /**
     * @return array
     */
    public function provideLangAndTranslation()
    {
        return array(
            array('en', array('en' => 'English', 'fr' => 'Francais')),
            array('fr', array('en' => 'English', 'fr' => 'Francais')),
        );
    }
}
