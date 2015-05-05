<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Context\GroupContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class TransformerManager
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class TransformerManager
{
    protected $transformers = array();
    protected $groupContext;
    protected $router;

    /**
     * @param UrlGeneratorInterface $router
     * @param GroupContext          $groupContext
     */
    public function __construct(UrlGeneratorInterface $router, GroupContext $groupContext)
    {
        $this->router = $router;
        $this->groupContext = $groupContext;
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

    /**
     * @return GroupContext
     */
    public function getGroupContext()
    {
        return $this->groupContext;
    }
}
