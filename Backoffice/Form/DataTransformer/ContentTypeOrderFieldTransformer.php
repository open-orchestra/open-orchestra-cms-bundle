<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ContentTypeOrderFieldTransformer
 */
class ContentTypeOrderFieldTransformer implements DataTransformerInterface
{
    /**
     * @param ArrayCollection $data
     *
     * @return ArrayCollection
     */
    public function transform($data)
    {
        return $data;
    }

    /**
     * @param ArrayCollection $data
     *
     * @return ArrayCollection
     */
    public function reverseTransform($data)
    {
        $fields = $data->toArray();
        uasort($fields, function (FieldTypeInterface $field1, FieldTypeInterface $field2) {
            return $field1->getPosition() >= $field2->getPosition() ? 1 : -1;
        });
        $fields = array_values($fields);

        return new ArrayCollection($fields);
    }
}
