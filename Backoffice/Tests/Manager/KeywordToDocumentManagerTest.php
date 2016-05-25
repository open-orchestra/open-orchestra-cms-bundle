<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;

/**
 * Class KeywordToDocumentManagerTest
 */
class KeywordToDocumentManagerTest extends AbstractBaseTestCase
{
    protected $manager;
    protected $keywordRepository;
    protected $suppressSpecialCharacterHelper;
    protected $keywordClass;
    protected $authorizationChecker;

    /**
     * Set Up the test
     */
    public function setUp()
    {
        $this->manager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');
        Phake::when($this->keywordRepository)->getManager()->thenReturn($this->manager);
        $this->suppressSpecialCharacterHelper = Phake::mock('OpenOrchestra\ModelInterface\Helper\SuppressSpecialCharacterHelperInterface');
        $this->keywordClass = 'OpenOrchestra\Backoffice\Tests\Manager\FakeKeyword';
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
    }

    /**
     * @param string                $keyword
     * @param boolean               $isGranted
     * @param KeywordInterface|null $keywordEntity
     * @param integer               $created
     *
     * @dataProvider provideKeyword
     */
    public function testGetDocument($keyword, $isGranted, $keywordEntity, $created)
    {
        Phake::when($this->suppressSpecialCharacterHelper)->transform($keyword)->thenReturn($keyword);
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($isGranted);
        Phake::when($this->keywordRepository)->findOneByLabel($keyword)->thenReturn($keywordEntity);

        $manager = new KeywordToDocumentManager(
            $this->keywordRepository,
            $this->suppressSpecialCharacterHelper,
            $this->keywordClass,
            $this->authorizationChecker
         );
        $manager->getDocument($keyword);

        Phake::verify($this->manager, Phake::times($created))->persist(Phake::anyParameters());
        Phake::verify($this->manager, Phake::times($created))->flush(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideKeyword()
    {
        return array(
            array('keyword_existing', true, Phake::mock('OpenOrchestra\Backoffice\Tests\Manager\FakeKeyword'), 0),
            array('keyword_existing', false, Phake::mock('OpenOrchestra\Backoffice\Tests\Manager\FakeKeyword'), 0),
            array('keyword_not_existing', true, null, 1),
            array('keyword_not_existing', false, null, 0),
        );
    }
}

class FakeKeyword implements KeywordInterface
{
    protected $label;

    public function getId(){}

    public function getLabel() {
        return $this->label;
    }

    public function setLabel($label) {
        $this->label = $label;
    }
}
