<?php

namespace OpenOrchestra\BackofficeBundle\Tests\DataTransformer;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\SuppressSpecialCharacterTransformer;

/**
 * Class SuppressSpecialCharacterTransformerTest
 */
class SuppressSpecialCharacterTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SuppressSpecialCharacterTransformer
     */
    protected $transform;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transform = new SuppressSpecialCharacterTransformer();
    }

    /**
     * @param string $valeur
     * @param string $expected
     *
     * @dataProvider generateStringWithSpecialChar
     */
    public function testTransform($valeur, $expected)
    {
        $this->assertSame($expected, $this->transform->transform($valeur));
    }

    /**
     * @return array
     */
    public function generateStringWithSpecialChar()
    {
        return array(
            array('testà', 'testa'),
            array('testétès', 'testetes'),
            array('test@ébètîs/têst', 'testebetistest'),
            array('test@é&è\tîs/têst', 'testeetistest'),
            array('_e<s>t@é&è\tîs/tês=t', 'esteetistest'),
            array('@&;:!%*$+-/_-()[]{}~#\\', ''),
            array('Spécial@chàractère', 'Specialcharactere'),
        );
    }
}
