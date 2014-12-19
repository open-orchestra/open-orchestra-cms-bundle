<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use PHPOrchestra\ModelInterface\Model\EmbedStatusInterface;
use PHPOrchestra\ModelInterface\Model\StatusInterface;
use PHPOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EmbedStatusToStatusTransformer
 */
class EmbedStatusToStatusTransformer implements DataTransformerInterface
{
    protected $statusRepositoy;
    protected $embedStatusClass;

    /**
     * @param StatusRepositoryInterface $statusRepository
     * @param string                    $embedStatusClass
     */
    public function __construct(StatusRepositoryInterface $statusRepository, $embedStatusClass)
    {
        $this->statusRepositoy = $statusRepository;
        $this->embedStatusClass = $embedStatusClass;
    }

    /**
     * @param EmbedStatusInterface $value
     *
     * @return StatusInterface
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if ($value instanceof EmbedStatusInterface) {
            return $this->statusRepositoy->find($value->getId());
        }

        return '';
    }

    /**
     * @param StatusInterface $value
     *
     * @return EmbedStatusInterface
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        $embedStatusClass = $this->embedStatusClass;

        return $embedStatusClass::createFromStatus($value);
    }
}
