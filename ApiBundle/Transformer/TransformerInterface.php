<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;

/**
 * Interface TransformerInterface
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
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
