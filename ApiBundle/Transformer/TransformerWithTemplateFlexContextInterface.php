<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;

/**
 * Interface TransformerWithTemplateFlexContextInterface
 */
interface TransformerWithTemplateFlexContextInterface
{
    /**
     * @param AreaFlexInterface          $area
     *
     * @return FacadeInterface
     */
    public function transformFromFlexTemplate(AreaFlexInterface $area);
}
