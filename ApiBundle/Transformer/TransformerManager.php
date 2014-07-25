<?php

namespace PHPOrchestra\ApiBundle\Transformer;

/**
 * Class TransformerManager
 */
class TransformerManager
{
    protected $transformers = array();

    /**
     * @param TransformerInterface $transformer
     */
    public function addTransformer(TransformerInterface $transformer)
    {
        $this->transformers[$transformer->getName()] = $transformer;
        $transformer->setContext($this);
    }

    /**
     * @param string $name
     *
     * @return TransformerInterface
     */
    public function get($name)
    {
        return $this->transformers[$name];
    }
}
