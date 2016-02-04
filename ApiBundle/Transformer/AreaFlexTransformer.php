<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AreaTransformer
 */
class AreaFlexTransformer extends AbstractSecurityCheckerAwareTransformer implements TransformerWithTemplateFlexContextInterface
{
    protected $nodeRepository;
    protected $areaManager;
    protected $currentSiteManager;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($facadeClass, $authorizationChecker);
    }

    /**
     * @param AreaFlexInterface          $area
     * @param TemplateFlexInterface|null $template
     * @param string|null                $parentAreaId
     *
     * @return FacadeInterface
     */
    public function transformFromFlexTemplate(AreaFlexInterface $area, TemplateFlexInterface $template = null, $parentAreaId = null)
    {
        $facade = $this->newFacade();

        $facade->label = $area->getLabel();
        $facade->areaId = $area->getAreaId();
        foreach ($area->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area_flex')->transformFromFlexTemplate($subArea, $template, $area->getAreaId()));
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
