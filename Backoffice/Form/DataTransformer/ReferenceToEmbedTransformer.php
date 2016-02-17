<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Manager\EntityDbMapperInterface;

/**
 * Class ReferenceToEmbedTransformer
 */
class ReferenceToEmbedTransformer implements DataTransformerInterface
{
    protected $entityDbMapper;
    protected $formTypeName;

    /**
     * @param EntityDbMapperInterface $entityDbMapper
     * @param ObjectManager           $objectManager
     * @param string                  $documentClass
     */
    public function __construct(EntityDbMapperInterface $entityDbMapper, ObjectManager $objectManager, $documentClass)
    {
        $this->entityDbMapper = $entityDbMapper;
        $this->objectManager = $objectManager;
        $this->documentClass = $documentClass;
    }

    /**
     * @param string $formTypeName
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
     * @return array|null
     */
    public function transform($data)
    {
        if (!is_null($data)) {
            return array($this->formTypeName => $this->entityDbMapper->fromDbToEntity($data)->getId());
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
        $document = $this->objectManager->find($this->documentClass, $id);

        return $this->entityDbMapper->fromEntityToDb($document);
    }
}
