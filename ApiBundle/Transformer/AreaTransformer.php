<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\AreaTransformerHttpException;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\AreaInterface;

/**
 * Class AreaTransformer
 */
class AreaTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param AreaInterface      $area
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     * @throws AreaTransformerHttpException
     */
    public function transform($area)
    {
        $facade = $this->newFacade();

        if (!$area instanceof AreaInterface) {
            throw new TransformerParameterTypeException();
        }

        foreach ($area->getBlocks() as $block) {
            $facade->addBlock($this->getTransformer('block')->transform($block));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area';
    }
}
