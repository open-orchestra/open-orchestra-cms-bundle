<?php

namespace OpenOrchestra\BackofficeBundle\Tests\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer;
use OpenOrchestra\ModelInterface\Model\EmbedKeywordInterface;

/**
 * Class EmbedKeywordsToKeywordsTransformerTest
 */
class EmbedKeywordsToKeywordsTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmbedKeywordsToKeywordsTransformer
     */
    protected $transformer;

    protected $keywordClass;
    protected $documentManager;
    protected $keywordRepository;
    protected $embedKeywordClass;
    protected $suppressSpecialCharacter;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->embedKeywordClass = 'OpenOrchestra\ModelBundle\Document\EmbedKeyword';
        $this->keywordClass = 'OpenOrchestra\ModelBundle\Document\Keyword';

        $this->documentManager = Phake::mock('Doctrine\ODM\MongoDB\DocumentManager');
        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');
        Phake::when($this->keywordRepository)->getManager()->thenReturn($this->documentManager);

        $this->suppressSpecialCharacter = Phake::mock('OpenOrchestra\BackofficeBundle\Form\DataTransformer\SuppressSpecialCharacterTransformer');

        $this->transformer = new EmbedKeywordsToKeywordsTransformer($this->keywordRepository, $this->suppressSpecialCharacter, $this->embedKeywordClass, $this->keywordClass);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * Test with null data
     */
    public function testTransformWithNullData()
    {
        $this->assertSame('', $this->transformer->transform(null));
    }

    /**
     * @param string $string
     *
     * @dataProvider providerDifferentString
     */
    public function testTransformWithStringData($string)
    {
        $this->assertSame($string, $this->transformer->transform($string));
    }

    /**
     * @return array
     */
    public function providerDifferentString()
    {
        return array(
            array('Dolor'),
            array('Sit'),
            array('Sit,Dolor'),
        );
    }

    /**
     * @param string $tagLabel
     *
     * @dataProvider provideTagLabel
     */
    public function testTransformWithTag($tagLabel)
    {
        $keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($keyword)->getLabel()->thenReturn($tagLabel);
        $keywords = new ArrayCollection();
        $keywords->add($keyword);
        $keywords->add($keyword);

        $this->assertSame($tagLabel . ',' . $tagLabel, $this->transformer->transform($keywords));
    }

    /**
     * @return array
     */
    public function provideTagLabel()
    {
        return array(
            array('tàg@=!', 'tag'),
            array('làbèl$&', 'label'),
        );
    }

    /**
     * Test with no data
     */
    public function testReverseTransformWithNullData()
    {
        Phake::when($this->suppressSpecialCharacter)->transform('')->thenReturn('');

        $embedKeywords = $this->transformer->reverseTransform('');

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $embedKeywords);
        $this->assertCount(0, $embedKeywords);

        Phake::verify($this->suppressSpecialCharacter)->transform('');
    }

    /**
     * @param string $tag
     * @param string $tagLabel
     *
     * @dataProvider provideTagLabel
     */
    public function testReverseTransformWithExistingTag($tag, $tagLabel)
    {
        Phake::when($this->suppressSpecialCharacter)->transform($tag)->thenReturn($tagLabel);

        $keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($keyword)->getLabel()->thenReturn($tagLabel);
        Phake::when($this->keywordRepository)->findOneByLabel(Phake::anyParameters())->thenReturn($keyword);

        $embedKeywords = $this->transformer->reverseTransform($tag . ',' . $tag);

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $embedKeywords);
        $this->assertCount(2, $embedKeywords);
        $this->assertSameKeyword($tagLabel, $embedKeywords->get(0));
        $this->assertSameKeyword($tagLabel, $embedKeywords->get(1));

        Phake::verify($this->suppressSpecialCharacter, Phake::times(2))->transform($tag);
    }

    /**
     * @param string $tagLabel
     *
     * @dataProvider provideTagLabel
     */
    public function testReverseTransformWithNonExistingTag($tag, $tagLabel)
    {
        Phake::when($this->suppressSpecialCharacter)->transform($tag)->thenReturn($tagLabel);

        Phake::when($this->keywordRepository)->findOneByLabel(Phake::anyParameters())->thenReturn(null);

        $embedKeywords = $this->transformer->reverseTransform($tag . ',' . $tag);

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $embedKeywords);
        $this->assertCount(2, $embedKeywords);
        $this->assertSameKeyword($tagLabel, $embedKeywords->get(0));
        $this->assertSameKeyword($tagLabel, $embedKeywords->get(1));
        Phake::verify($this->suppressSpecialCharacter, Phake::times(2))->transform($tag);
        Phake::verify($this->documentManager, Phake::times(2))->persist(Phake::anyParameters());
        Phake::verify($this->documentManager, Phake::times(2))->flush(Phake::anyParameters());
        Phake::verify($this->documentManager, Phake::never())->flush();
    }

    /**
     * @param string                $tagLabel
     * @param EmbedKeywordInterface $embedKeyword
     */
    protected function assertSameKeyword($tagLabel, $embedKeyword)
    {
        $this->assertSame($tagLabel, $embedKeyword->getLabel());
        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\EmbedKeywordInterface', $embedKeyword);
    }
}
