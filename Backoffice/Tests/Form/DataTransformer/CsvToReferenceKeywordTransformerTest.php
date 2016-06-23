<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\DataTransformer\CsvToReferenceKeywordTransformer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CsvToReferenceKeywordTransformerTest
 */
class CsvToReferenceKeywordTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var CsvToReferenceKeywordTransformer
     */
    protected $transformer;
    protected $keywordToDocumentManager;
    protected $keywordRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->keywordToDocumentManager = Phake::mock('OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager');
        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');

        $catX1Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($catX1Keyword)->getLabel()->thenReturn('cat:X1');
        Phake::when($catX1Keyword)->getId()->thenReturn('fakeId[cat:X1]');
        $catX2Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($catX2Keyword)->getLabel()->thenReturn('cat:X2');
        Phake::when($catX2Keyword)->getId()->thenReturn('fakeId[cat:X2]');
        $authorAAAKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($authorAAAKeyword)->getLabel()->thenReturn('author:AAA');
        Phake::when($authorAAAKeyword)->getId()->thenReturn('fakeId[author:AAA]');
        $t1Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($t1Keyword)->getLabel()->thenReturn('T1');
        Phake::when($t1Keyword)->getId()->thenReturn('fakeId[T1]');
        $t2Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($t2Keyword)->getLabel()->thenReturn('T2');
        Phake::when($t2Keyword)->getId()->thenReturn('fakeId[T2]');
        $t3Keyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($t3Keyword)->getLabel()->thenReturn('T3');
        Phake::when($t3Keyword)->getId()->thenReturn('fakeId[T3]');
        $notCreatedKeyword = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        Phake::when($notCreatedKeyword)->getLabel()->thenReturn('not_created_keyword');
        Phake::when($notCreatedKeyword)->getId()->thenReturn('fakeId[not_created_keyword]');

        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');
        Phake::when($this->keywordRepository)->find('fakeId[cat:X1]')->thenReturn($catX1Keyword);
        Phake::when($this->keywordRepository)->find('fakeId[cat:X2]')->thenReturn($catX2Keyword);
        Phake::when($this->keywordRepository)->find('fakeId[author:AAA]')->thenReturn($authorAAAKeyword);
        Phake::when($this->keywordRepository)->find('fakeId[T1]')->thenReturn($t1Keyword);
        Phake::when($this->keywordRepository)->find('fakeId[T2]')->thenReturn($t2Keyword);
        Phake::when($this->keywordRepository)->find('fakeId[T3]')->thenReturn($t3Keyword);

        $this->keywordToDocumentManager = Phake::mock('OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager');
        Phake::when($this->keywordToDocumentManager)->getDocument('not_created_keyword')->thenReturn($notCreatedKeyword);

        $this->transformer = new CsvToReferenceKeywordTransformer($this->keywordToDocumentManager, $this->keywordRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * @param string $string
     * @param array  $keywords
     *
     * @dataProvider providerReverseTransformData
     */
    public function testReverseTransform($string, $keywords)
    {
        $keywords = $this->createKeywordsToInterface($keywords);

        $this->assertSame($keywords, $this->transformer->reverseTransform($string)->toArray());
    }

    /**
     * @return array
     */
    public function providerReverseTransformData()
    {
        return array(
            array('cat:X1', array('cat:X1')),
            array('cat:X1,cat:X2,author:AAA,T1,T2,T3', array('cat:X1', 'cat:X2', 'author:AAA', 'T1', 'T2', 'T3')),
        );
    }

    /**
     * @param array  $keywords
     * @param string $expected
     *
     * @dataProvider providerTransformData
     */
    public function testTransform($keywords, $expected)
    {
        $keywords = $this->createKeywordsToInterface($keywords);
        $this->assertSame($expected, $this->transformer->transform(new ArrayCollection($keywords)));
    }

    /**
     * @return array
     */
    public function providerTransformData()
    {
        return array(
            array(array('cat:X1', 'cat:X2', 'author:AAA', 'T1', 'T2', 'T3'), 'cat:X1,cat:X2,author:AAA,T1,T2,T3'),
            array(array('cat:X1'), 'cat:X1'),
            array(array(), ''),
        );
    }

    /**
     * @param array  $keywords
     *
     * @return array|null
     */
    protected function createKeywordsToInterface($keywords)
    {
        if (!is_null($keywords)) {
            $keywordsInterface = array();

            foreach ($keywords as $key => $keyword) {
                $keywordsInterface[$key] = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
                Phake::when($keywordsInterface[$key])->getLabel()->thenReturn($keyword);
                Phake::when($keywordsInterface[$key])->getId()->thenReturn('fakeId[' . $keyword . ']');
                Phake::when($this->keywordRepository)->find('fakeId[' . $keyword . ']')->thenReturn($keywordsInterface[$key]);
                Phake::when($this->keywordToDocumentManager)->getDocument($keyword)->thenReturn($keywordsInterface[$key]);
            }

            return $keywordsInterface;
        }

        return null;
    }
}
