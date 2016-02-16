<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplateFlexPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;

/**
 * Class AreaTransformer
 */
class AreaFlexTransformer extends AbstractSecurityCheckerAwareTransformer implements TransformerWithTemplateFlexContextInterface
{
    /**
     * @param AreaFlexInterface     $area
     * @param TemplateFlexInterface $template
     * @param string|null           $parentAreaId
     *
     * @return FacadeInterface
     */
    public function transformFromTemplateFlex(AreaFlexInterface $area, TemplateFlexInterface $template, $parentAreaId = null)
    {
        $facade = $this->newFacade();
        $facade->label = $area->getLabel();
        $facade->areaId = $area->getAreaId();
        $facade->areaType = $area->getAreaType();
        $facade->width = $area->getWidth();
        $facade->label = $area->getLabel();

        foreach ($area->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area_flex')->transformFromTemplateFlex($subArea, $template, $area->getAreaId()));
        }
        if ($this->authorizationChecker->isGranted(TreeTemplateFlexPanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE_FLEX, $template)) {
            $facade->addLink('_self_form_new_row', $this->generateRoute('open_orchestra_backoffice_new_row_area_flex', array(
                'templateId' => $template->getTemplateId(),
                'areaParentId' => $area->getAreaId(),
            )));

            if (AreaFlexInterface::TYPE_COLUMN === $area->getAreaType()) {
                $facade->addLink('_self_form_column', $this->generateRoute('open_orchestra_backoffice_area_flex_form_column', array(
                    'templateId' => $template->getTemplateId(),
                    'areaId' => $area->getAreaId(),
                )));

                $facade->addLink('_self_delete_column', $this->generateRoute('open_orchestra_api_area_flex_column_delete_in_template', array(
                    'templateId' => $template->getTemplateId(),
                    'areaId' => $area->getAreaId(),
                    'parentId' => $parentAreaId,
                )));

                $facade->addLink('_self_delete_row', $this->generateRoute('open_orchestra_api_area_flex_row_delete_in_template', array(
                    'templateId' => $template->getTemplateId(),
                    'areaId' => $parentAreaId,
                )));
            }
        }

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
