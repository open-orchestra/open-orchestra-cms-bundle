<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class ContentTypeManager
 */
class ContentTypeManager
{
    protected $contentTypeClass;

    /**
     * @param string $contentTypeClass
     */
    public function __construct($contentTypeClass)
    {
        $this->contentTypeClass = $contentTypeClass;
    }

    /**
     * @return ContentTypeInterface
     */
    public function initializeNewContentType()
    {
        $contentTypeClass = $this->contentTypeClass;
        /** @var ContentTypeInterface $contentType */
        $contentType = new $contentTypeClass();
        $contentType->setDefaultListable($this->getDefaultListableColumns());

        return $contentType;
    }

    /**
     * @param ContentTypeInterface $contentType
     *
     * @return ContentTypeInterface
     */
    public function duplicate(ContentTypeInterface $contentType)
    {
        $newContentType = clone $contentType;
        $newContentType->setDefaultListable($this->updateDefaultListable($newContentType->getDefaultListable()));

        foreach ($contentType->getFields() as $field) {
            $newField = clone $field;
            foreach ($field->getOptions() as $option) {
                $newOption = clone $option;
                $newField->addOption($newOption);
            }

            $newContentType->addFieldType($newField);
        }

        return $newContentType;
    }

    /**
     * @param array $contentTypeDefaultListable
     *
     * @return array
     */
    protected function updateDefaultListable(array $contentTypeDefaultListable)
    {
        $defaultListable = $this->getDefaultListableColumns();

        $toKeep = array_intersect_key($contentTypeDefaultListable, $defaultListable);
        $toAdd = array_diff_key($defaultListable, $toKeep);

        return array_merge($toKeep, $toAdd);
    }

    /**
     * @param array $contentTypes
     */
    public function delete($contentTypes)
    {
        if (!empty($contentTypes)) {
            foreach ($contentTypes as $contentType)
            {
                $contentType->setDeleted(true);
            }
        }
    }

    /**
     * @return array
     */
    protected function getDefaultListableColumns()
    {
        return array(
            'name'           => true,
            'linked_to_site' => false,
            'created_at'     => false,
            'created_by'     => true,
            'updated_at'     => true,
            'updated_by'     => false,
            'status'         => true,
        );
    }
}
