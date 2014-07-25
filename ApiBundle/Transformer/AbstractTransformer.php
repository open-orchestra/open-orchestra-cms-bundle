<?php

namespace PHPOrchestra\ApiBundle\Transformer;

/**
 * Class AbstractTransformer
 */
abstract class AbstractTransformer implements TransformerInterface
{
    protected $context;

    /**
     * @param TransformerManager $manager
     */
    public function setContext(TransformerManager $manager)
    {
        $this->context = $manager;
    }

    /**
     * @param string $name
     *
     * @return TransformerInterface
     */
    protected function getTransformer($name)
    {
        return $this->context->get($name);
    }
}
