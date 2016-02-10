<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;

/**
 * Class AreaTransformer
 */
class AreaFlexTransformer extends AbstractTransformer implements TransformerWithTemplateFlexContextInterface
{
    /**
     * @param AreaFlexInterface     $area
     * @param TemplateFlexInterface $template
     *
     * @return FacadeInterface
     */
    public function transformFromTemplateFlex(AreaFlexInterface $area, TemplateFlexInterface $template)
    {
        $facade = $this->newFacade();
        $facade->label = $area->getLabel();
        $facade->areaId = $area->getAreaId();
        $facade->areaType = $area->getAreaType();
        $facade->width = $area->getWidth();
        foreach ($area->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area_flex')->transformFromTemplateFlex($subArea, $template));
        }

        $facade->addLink('_self_form_new_row', $this->generateRoute('open_orchestra_backoffice_new_row_area_flex',array(
            'templateId' => $template->getTemplateId(),
            'areaParentId' => $area->getAreaId(),
        )));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area_flex';
    }
}
