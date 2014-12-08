<?php

namespace PHPOrchestra\BackofficeBundle\Test\DataTransformer;

use PHPOrchestra\ModelBundle\Document\EmbedKeyword;

use PHPOrchestra\ModelBundle\Document\EmbedStatus;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer;

/**
 * Class EmbedKeywordsToKeywordsTransformerTest
 */
class EmbedKeywordsToKeywordsTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmbedKeywordsToKeywordsTransformer
     */
    protected $transformer;

    protected $keyword1Id;
    protected $keyword2Id;
    protected $keywords;
    protected $keyword1;
    protected $keyword2;
    protected $embedKeywords;
    protected $embedKeyword1;
    protected $embedKeyword2;
    protected $keywordRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->keyword1Id = 'keyword1Id';
        $this->keyword2Id = 'keyword2Id';

        $this->keywords = new ArrayCollection();

        $this->keyword1 = Phake::mock('PHPOrchestra\ModelBundle\Document\Keyword');
        Phake::when($this->keyword1)->getId()->thenReturn($this->keyword1Id);
        $this->keywords->add($this->keyword1);

        $this->keyword2 = Phake::mock('PHPOrchestra\ModelBundle\Document\Keyword');
        Phake::when($this->keyword2)->getId()->thenReturn($this->keyword2Id);
        $this->keywords->add($this->keyword2);

        $this->embedKeywords = new ArrayCollection();

        $this->embedKeyword1 = Phake::mock('PHPOrchestra\ModelBundle\Document\EmbedKeyword');
        Phake::when($this->embedKeyword1)->getId()->thenReturn($this->keyword1Id);
        $this->embedKeywords->add($this->embedKeyword1);

        $this->embedKeyword2 = Phake::mock('PHPOrchestra\ModelBundle\Document\EmbedKeyword');
        Phake::when($this->embedKeyword2)->getId()->thenReturn($this->keyword2Id);
        $this->embedKeywords->add($this->embedKeyword2);
        
        $this->keywordRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\KeywordRepository');
        Phake::when($this->keywordRepository)->find($this->keyword1Id)->thenReturn($this->keyword1);
        Phake::when($this->keywordRepository)->find($this->keyword2Id)->thenReturn($this->keyword2);

        $this->transformer = new EmbedKeywordsToKeywordsTransformer($this->keywordRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * Test transform
     */
    public function testTransform()
    {
        $keywords = $this->transformer->transform($this->embedKeywords);

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $keywords);
        $this->assertEquals(2, count($keywords));
        $this->assertSame(serialize($this->keywords), serialize($keywords));
    }

    /**
     * Test reverse transform
     */
    public function testReverseTransform()
    {
        $embedKeywords = $this->transformer->reverseTransform($this->keywords);

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $embedKeywords);
        $this->assertEquals(2, count($embedKeywords));

        $this->assertSame($embedKeywords[0]->getId(), $this->keyword1Id);
        $this->assertSame($embedKeywords[1]->getId(), $this->keyword2Id);
    }
}
