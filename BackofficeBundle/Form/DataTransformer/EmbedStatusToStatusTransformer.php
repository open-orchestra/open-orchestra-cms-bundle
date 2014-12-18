<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use PHPOrchestra\ModelBundle\Repository\StatusRepository;
use PHPOrchestra\ModelInterface\Model\EmbedStatusInterface;
use PHPOrchestra\ModelInterface\Model\StatusInterface;
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
     * @param StatusRepository $statusRepository
     * @param string           $embedStatusClass
     */
    public function __construct(StatusRepository $statusRepository, $embedStatusClass)
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
