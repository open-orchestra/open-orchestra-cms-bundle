<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Manager\DocumentForEmbedManager;

/**
 * Class ReferenceToEmbedTransformer
 */
class ReferenceToEmbedTransformer implements DataTransformerInterface
{
    protected $documentForEmbedManager;
    protected $formTypeName;

    /**
     * @param ObjectManager $objectManager
     * @param string        $contentClass
     */
    public function __construct(DocumentForEmbedManager $documentForEmbedManager)
    {
        $this->documentForEmbedManager = $documentForEmbedManager;
    }

    /**
     * @param string $documentName
     */
    public function setFormTypeName($formTypeName)
    {
        $this->formTypeName = $formTypeName;
    }

    /**
     * Take a embed document array representation to return associative array formType id
     *
     * @param array $data
     *
     * @return array
     */
    public function transform($data)
    {
        if (!is_null($data)) {
            return array($this->formTypeName => $this->documentForEmbedManager->transform($data));
        }

        return null;
    }

    /**
     * Take an array with document id to turn it into embed document
     *
     * @param array $data
     *
     * @return array
     */
    public function reverseTransform($data)
    {
        list($key, $id) = each($data);

        return $this->documentForEmbedManager->reverseTransform($id);
    }
}
