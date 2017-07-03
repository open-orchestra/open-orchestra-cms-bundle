<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AreaTransformer
 */
class AreaTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $areaClass;

    /**
     * @param string                        $facadeClass
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $areaClass
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        $areaClass
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->areaClass = $areaClass;
    }

    /**
     * @param AreaInterface $area
     * @param array|null    $params
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($area, array $params = null)
    {
        $facade = $this->newFacade();

        if (!$area instanceof AreaInterface) {
            throw new TransformerParameterTypeException();
        }

        foreach ($area->getBlocks() as $block) {
            $facade->addBlock($this->getContext()->transform('block', $block));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array|null      $params
     *
     * @return AreaInterface
     */
    public function reverseTransform(FacadeInterface $facade, array $params = null)
    {
        /** @var AreaInterface $area */
        $area = new $this->areaClass();
        foreach ($facade->getBlocks() as $block) {
            $area->addBlock($this->getContext()->reverseTransform('block', $block));
        }

        return $area;
    }

    /**
     * @return string
     */
    public function isCached()
    {
        return $this->hasGroup(CMSGroupContext::AREAS);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area';
    }
}
