<?php

namespace OpenOrchestra\ApiBundle\Mapping;

use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class MappingContentAttribute
 */
class MappingContentAttribute
{
    /**
     * @var ContentTypeRepositoryInterface
     */
    protected $contentTypeRepository;

    /***
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     */
    public function __construct(ContentTypeRepositoryInterface $contentTypeRepository)
    {
        $this->contentTypeRepository = $contentTypeRepository;
    }

    /**
     * @param string $contentTypeId
     *
     * @return array
     */
    public function getMapping($contentTypeId)
    {
        $mapping = array();

        /**
         * @var ContentTypeInterface
         */
        $contentType  = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($contentTypeId);
        if (null !== $contentType) {
            /**
             * @var $field FieldTypeInterface
             */
            foreach ($contentType->getFields() as $field) {
                $fieldId = $field->getFieldId();
                $key = 'attributes.'.$fieldId.'.string_value';
                $mapping[$key] = array(
                    'field' => 'attributes.'.$fieldId.'.stringValue',
                    'key' => $key,
                    'type' => 'string'
                );
            }
        }

        return $mapping;
    }
}
