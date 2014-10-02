<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class TransformerManager
 */
class TransformerManager
{
    protected $transformers = array();
    protected $router;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

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

    /**
     * @return UrlGeneratorInterface
     */
    public function getRouter()
    {
        return $this->router;
    }
}
