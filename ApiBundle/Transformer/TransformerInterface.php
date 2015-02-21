<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;

/**
 * Interface TransformerInterface
 */
interface TransformerInterface
{
    /**
     * @param mixed $mixed
     * @return FacadeInterface
     */
    public function transform($mixed);

    /**
     * @param FacadeInterface $facade
     * @param mixed|null      $source
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param TransformerManager $manager
     */
    public function setContext(TransformerManager $manager);
}
