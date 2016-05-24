<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\DataTransformer\ConditionToReferenceKeywordTransformer;

/**
 * Class ConditionToReferenceKeywordTransformerTest
 */
class ConditionToReferenceKeywordTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var ConditionToReferenceKeywordTransformer
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
        Phake::when($this->keywordToDocumentManager)->getDocument('cat:X1')->thenReturn($catX1Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('cat:X2')->thenReturn($catX2Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('author:AAA')->thenReturn($authorAAAKeyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('T1')->thenReturn($t1Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('T2')->thenReturn($t2Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('T3')->thenReturn($t3Keyword);
        Phake::when($this->keywordToDocumentManager)->getDocument('not_created_keyword')->thenReturn($notCreatedKeyword);

        $this->transformer = new ConditionToReferenceKeywordTransformer($this->keywordToDocumentManager, $this->keywordRepository);
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
     *
     * @dataProvider providerReverseTransformData
     */
    public function testReverseTransform($string, $expected)
    {
        $this->assertSame($expected, $this->transformer->reverseTransform($string));
    }
    /**
     * @return array
     */
    public function providerReverseTransformData()
    {
        return array(
            array('( NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )', '( NOT ( ##fakeId[cat:X1]## OR ##fakeId[cat:X2]## ) AND ##fakeId[author:AAA]## ) OR ( ##fakeId[T1]## OR ##fakeId[T2]## OR NOT ##fakeId[T3]## )'),
            array('( cat:X1 OR cat:X2 ) AND ( author:AAA ) AND ( T1 OR T2 OR NOT T3 )', '( ##fakeId[cat:X1]## OR ##fakeId[cat:X2]## ) AND ( ##fakeId[author:AAA]## ) AND ( ##fakeId[T1]## OR ##fakeId[T2]## OR NOT ##fakeId[T3]## )'),
            array('cat:X1', '##fakeId[cat:X1]##'),
            array('( cat:X1 )', '( ##fakeId[cat:X1]## )'),
            array('not_created_keyword', '##fakeId[not_created_keyword]##'),
        );
    }

    /**
     * @param string $string
     *
     * @dataProvider providerTransformData
     */
    public function testTransform($string, $expected)
    {
        $this->assertSame($expected, $this->transformer->transform($string));
    }
    /**
     * @return array
     */
    public function providerTransformData()
    {
        return array(
            array('( NOT ( ##fakeId[cat:X1]## OR ##fakeId[cat:X2]## ) AND ##fakeId[author:AAA]## ) OR ( ##fakeId[T1]## OR ##fakeId[T2]## OR NOT ##fakeId[T3]## )', '( NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )'),
            array('( ##fakeId[cat:X1]## OR ##fakeId[cat:X2]## ) AND ( ##fakeId[author:AAA]## ) AND ( ##fakeId[T1]## OR ##fakeId[T2]## OR NOT ##fakeId[T3]## )', '( cat:X1 OR cat:X2 ) AND ( author:AAA ) AND ( T1 OR T2 OR NOT T3 )'),
            array('##fakeId[cat:X1]##', 'cat:X1'),
            array('( ##fakeId[cat:X1]## )', '( cat:X1 )'),
            array('##fakeId[not_created_keyword]##', ''),
            array(null, ''),
        );
    }
}
