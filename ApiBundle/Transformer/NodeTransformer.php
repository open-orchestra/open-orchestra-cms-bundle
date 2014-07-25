<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\NodeFacade;
use PHPOrchestra\ModelBundle\Model\NodeInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractTransformer
{
    /**
     * @param NodeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new NodeFacade();

        foreach ($mixed->getAreas() as $area) {
            $facade->addAreas($this->getTransformer('area')->transform($area, $mixed));
        }

        return $facade;
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
