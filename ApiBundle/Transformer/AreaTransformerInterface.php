<?php

namespace OpenOrchestra\ApiBundle\Transformer;


use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;

/**
 * Interface AreaTransformerInterface
 */
interface AreaTransformerInterface extends TransformerInterface
{
    /**
     * @param AreaInterface          $area
     * @param TemplateInterface|null $template
     * @param string|null            $parentAreaId
     *
     * @return FacadeInterface
     */
    public function transformFromTemplate($area, TemplateInterface $template = null, $parentAreaId = null);
}
