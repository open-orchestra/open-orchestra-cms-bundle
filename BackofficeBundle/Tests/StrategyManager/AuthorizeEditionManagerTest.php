<?php

namespace OpenOrchestra\BackofficeBundle\Tests\StrategyManager;

use OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeEditionManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Test AuthorizeEditionManagerTest
 */
class AuthorizeEditionManagerTest extends AbstractBaseTestCase
{
    /**
     * @var AuthorizeEditionManager
     */
    protected $manager;

    protected $strategy1;
    protected $strategy2;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy1 = Phake::mock('OpenOrchestra\Backoffice\AuthorizeEdition\AuthorizeEditionInterface');
        $this->strategy2 = Phake::mock('OpenOrchestra\Backoffice\AuthorizeEdition\AuthorizeEditionInterface');

        $this->manager = new AuthorizeEditionManager();
        $this->manager->addStrategy($this->strategy1);
        $this->manager->addStrategy($this->strategy2);
    }

    /**
     * @param bool $support1
     * @param bool $isEditable1
     * @param bool $support2
     * @param bool $isEditable2
     * @param bool $isEditable
     *
     * @dataProvider provideEditionDatas
     */
    public function testIsEditable($support1, $isEditable1, $support2, $isEditable2, $isEditable)
    {
        Phake::when($this->strategy1)->support(Phake::anyParameters())->thenReturn($support1);
        Phake::when($this->strategy1)->isEditable(Phake::anyParameters())->thenReturn($isEditable1);
        Phake::when($this->strategy2)->support(Phake::anyParameters())->thenReturn($support2);
        Phake::when($this->strategy2)->isEditable(Phake::anyParameters())->thenReturn($isEditable2);

        $this->assertSame($isEditable, $this->manager->isEditable(new \StdClass()));
    }

    /**
     * @return array
     */
    public function provideEditionDatas()
    {
        return array(
            array(true, true, true, true, true),
            array(true, true, true, false, false),
            array(true, false, true, false, false),
            array(true, false, false, false, false),
            array(true, false, false, true, false),
            array(false, false, false, false, true),
            array(false, true, false, true, true),
        );
    }
}
