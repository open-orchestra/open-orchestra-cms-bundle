<?php

namespace OpenOrchestra\ApiBundle\Transformer;


use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;

/**
 * Interface TransformerWithTemplateFlexContextInterface
 */
interface TransformerWithTemplateFlexContextInterface
{
    /**
     * @param AreaFlexInterface     $area
     * @param TemplateFlexInterface $template
     *
     * @return FacadeInterface
     */
    public function transformFromTemplateFlex(AreaFlexInterface $area, TemplateFlexInterface $template);
}
