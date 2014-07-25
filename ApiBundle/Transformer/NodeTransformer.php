<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractTransformer
{
    /**
     * @param mixed $mixed
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        // TODO: Implement transform() method.
    }

    /**
     * @param FacadeInterface $facade
     * @param mixed|null $source
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        // TODO: Implement reverseTransform() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node';
    }

}
