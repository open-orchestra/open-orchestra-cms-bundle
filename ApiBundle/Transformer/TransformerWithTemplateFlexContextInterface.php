<?php

namespace OpenOrchestra\ApiBundle\Transformer;


use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\TransformerInterface;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;

/**
 * Interface TransformerWithTemplateFlexContextInterface
 */
interface TransformerWithTemplateFlexContextInterface extends TransformerInterface
{
    /**
     * @param AreaFlexInterface          $area
     * @param TemplateFlexInterface|null $template
     * @param string|null                $parentAreaId
     *
     * @return FacadeInterface
     */
    public function transformFromFlexTemplate(AreaFlexInterface $area, TemplateFlexInterface $template = null, $parentAreaId = null);
}
