<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractTransformer
 */
abstract class AbstractTransformer implements TransformerInterface
{
    /**
     * @var TransformerManager
     */
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

    /**
     * @return UrlGenerator
     */
    protected function getRouter()
    {
        return $this->context->getRouter();
    }

    /**
     * @param string $name
     * @param array  $parameter
     *
     * @return string
     */
    protected function generateRoute($name, $parameter = array())
    {
        return $this->getRouter()->generate($name, $parameter, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param string $group
     *
     * @return bool
     */
    protected function hasGroup($group)
    {
        return $this->context->getGroupContext()->hasGroup($group);
    }

    /**
     * @param mixed $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
    }

    /**
     * @param FacadeInterface $facade
     * @param mixed|null      $source
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
    }
}
