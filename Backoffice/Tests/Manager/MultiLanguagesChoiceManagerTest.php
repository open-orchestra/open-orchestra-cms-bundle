<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\MultiLanguagesChoiceManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class MultiLanguagesChoiceManagerTest
 */
class MultiLanguagesChoiceManagerTest extends AbstractBaseTestCase
{
    /**
     * @var MultiLanguagesChoiceManager
     */
    protected $manager;

    protected $names;
    protected $object;
    protected $baseValue;
    protected $contextManager;
    protected $translator;
    protected $noTranslation = 'no translation';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->baseValue = array('en' => 'English', 'fr' => 'Francais');

        $this->names = array("en" => $this->baseValue['en'], "fr" => $this->baseValue['fr']);

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->noTranslation);

        $this->manager = new MultiLanguagesChoiceManager($this->contextManager, $this->translator);
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
        $this->assertSame($this->noTranslation, $this->manager->choose(array()));
    }

    /**
     * Test with new language
     */
    public function testChooseWithNotIncludedLanguage()
    {
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn('de');

        $this->assertSame($this->noTranslation, $this->manager->choose($this->names));
    }
}
