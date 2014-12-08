<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use PHPOrchestra\ModelBundle\Document\EmbedStatus;
use PHPOrchestra\ModelBundle\Document\Status;
use PHPOrchestra\ModelBundle\Repository\StatusRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EmbedStatusToStatusTransformer
 */
class EmbedStatusToStatusTransformer implements DataTransformerInterface
{
    protected $statusRepositoy;

    /**
     * @param StatusRepository $statusRepository
     */
    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepositoy = $statusRepository;
    }

    /**
     * @param EmbedStatus $value
     *
     * @return Status
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if ($value instanceof EmbedStatus) {
            return $this->statusRepositoy->find($value->getId());
        }

        return '';
    }

    /**
     * @param Status $value
     *
     * @return EmbedStatus
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        return EmbedStatus::createFromStatus($value);
    }
}
