<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ReferenceToEmbedTransformer
 */
class ReferenceToEmbedTransformer implements DataTransformerInterface
{
    protected $objectManager;
    protected $documentClass;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $documentClass
     */
    public function setDocumentClass($documentClass)
    {
        $this->documentClass = $documentClass;
    }

    /**
     * Take a Document Id to turn it into Embed Document
     *
     * @param string $id
     *
     * @return Document
     */
    public function transform($id)
    {
        $document = $this->objectManager->find($this->documentClass, $id);

        return $document;
    }

    /**
     * Take an array with document id to turn it into embed document
     *
     * @param array $assiociatedId
     *
     * @return Document
     */
    public function reverseTransform($value)
    {
        list($documentClass, $id) = each($value);

        $document = $this->objectManager->find(str_replace(':', '\\', $documentClass), $id);

        return $document;
    }
}
