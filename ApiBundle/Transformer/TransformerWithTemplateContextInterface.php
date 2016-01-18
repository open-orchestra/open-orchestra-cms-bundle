<?php

namespace OpenOrchestra\ApiBundle\Transformer;


use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\TransformerInterface;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;

/**
 * Interface AreaTransformerInterface
 */
interface TransformerWithTemplateContextInterface extends TransformerInterface
{
    /**
     * @param AreaInterface          $area
     * @param TemplateInterface|null $template
     * @param string|null            $parentAreaId
     *
     * @return FacadeInterface
     */
    public function transformFromTemplate(AreaInterface $area, TemplateInterface $template = null, $parentAreaId = null);
}
