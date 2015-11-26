<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\Persisters\PersistenceBuilder;

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
    public function transform($document)
    {
        if (!is_null($document)) {
            return array(
                str_replace('\\', ':', $this->documentClass) => $document['_id']->{'$id'}
            );
        }

        return null;
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
        $documentClass = str_replace(':', '\\', $documentClass);

        $document = $this->objectManager->find($documentClass, $id);
        $unitOfWork = $this->objectManager->getUnitOfWork();
        $persistenceBuilder = new PersistenceBuilder($this->objectManager, $unitOfWork);
        $mapping = array (
            'targetDocument' => $documentClass,
        );

        return $persistenceBuilder->prepareEmbeddedDocumentValue($mapping, $document, true);
    }
}
