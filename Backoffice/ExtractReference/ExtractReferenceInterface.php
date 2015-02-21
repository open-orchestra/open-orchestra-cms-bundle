<?php

namespace OpenOrchestra\Backoffice\ExtractReference;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Interface ExtractReferenceInterface
 */
interface ExtractReferenceInterface
{
    /**
     * @param StatusableInterface $statusableElement
     *
     * @return bool
     */
    public function support(StatusableInterface $statusableElement);

    /**
     * @param StatusableInterface $statusableElement
     *
     * @return array
     */
    public function extractReference(StatusableInterface $statusableElement);

    /**
     * @return string
     */
    public function getName();
}
