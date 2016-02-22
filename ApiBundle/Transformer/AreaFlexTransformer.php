<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplateFlexPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;
use UnexpectedValueException;

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
            $facade->addLink('_self_move_area', $this->generateRoute('open_orchestra_api_area_flex_move_in_template', array(
                'areaParentId' => $area->getAreaId(),
                'templateId' => $template->getTemplateId(),
            )));

            if (AreaFlexInterface::TYPE_COLUMN === $area->getAreaType()) {
                $facade->addLink('_self_form_column', $this->generateRoute('open_orchestra_backoffice_area_flex_form_column', array(
                    'templateId' => $template->getTemplateId(),
                    'areaId' => $area->getAreaId(),
                )));

                $facade->addLink('_self_form_row', $this->generateRoute('open_orchestra_backoffice_area_flex_form_row', array(
                    'templateId' => $template->getTemplateId(),
                    'areaId' => $parentAreaId,
                )));

                $facade->addLink('_self_delete_column', $this->generateRoute('open_orchestra_api_area_flex_column_delete_in_template', array(
                    'templateId' => $template->getTemplateId(),
                    'areaId' => $area->getAreaId(),
                    'areaParentId' => $parentAreaId,
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
     * @param FacadeInterface        $facade
     * @param AreaFlexInterface|null $source
     *
     * @return AreaFlexInterface
     *
     * @throws UnexpectedValueException
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (!$source instanceof AreaFlexInterface) {
            throw new UnexpectedValueException("source must be an instance of AreaFlexInterface");
        }
        $subAreaFacade = $facade->getAreas();
        $newOrderSubAreas = array();
        /** @var AreaFlexInterface $subArea */
        foreach ($source->getAreas() as $subArea) {
            $order = $this->getAreaOrderInChildren($subArea->getAreaId(), $subAreaFacade);
            $newOrderSubAreas[$order] = $subArea;
        }
        ksort($newOrderSubAreas);
        $source->setAreas(new ArrayCollection($newOrderSubAreas));

        return $source;
    }

    /**
     * @param string $areaId
     * @param array  $areasChildren
     *
     * @return int
     */
    protected function getAreaOrderInChildren($areaId, array $areasChildren)
    {
        foreach ($areasChildren as $areaFacade) {
            if ($areaId === $areaFacade->areaId)
                return $areaFacade->order;
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area_flex';
    }
}
