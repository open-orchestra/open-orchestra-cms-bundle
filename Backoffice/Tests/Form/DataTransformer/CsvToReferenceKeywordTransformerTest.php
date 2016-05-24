<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\DataTransformer\CsvToReferenceKeywordTransformer;
use Doctrine\Common\Collections\ArrayCollection ;

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

        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');

        $this->keywordToDocumentManager = Phake::mock('OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager');

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
            array('cat:X1,cat:X2,author:AAA,T1,T2,T3', array('cat:X1', 'cat:X2', 'author:AAA', 'T1', 'T2', 'T3')),
            array('cat:X1', array('cat:X1')),
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
        $this->assertSame($expected, $this->transformer->transform($keywords));
    }

    /**
     * @return array
     */
    public function providerTransformData()
    {
        return array(
            array(array('cat:X1', 'cat:X2', 'author:AAA', 'T1', 'T2', 'T3'), 'cat:X1,cat:X2,author:AAA,T1,T2,T3'),
            array(array('cat:X1'), 'cat:X1'),
            array(null, ''),
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
