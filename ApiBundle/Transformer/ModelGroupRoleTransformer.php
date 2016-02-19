<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\RoleNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Collector\RoleCollectorInterface;
use OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class ModelGroupRoleTransformer
 */
class ModelGroupRoleTransformer extends AbstractTransformer implements TransformerWithGroupInterface
{
    protected $modelGroupRoleClass;
    protected $collector;

    /**
     * @param string                 $facadeClass
     * @param string                 $modelGroupRoleClass
     * @param RoleCollectorInterface $collector
     */
    public function __construct(
        $facadeClass,
        $modelGroupRoleClass,
        RoleCollectorInterface $collector
    ) {
        parent::__construct($facadeClass);
        $this->modelGroupRoleClass = $modelGroupRoleClass;
        $this->collector = $collector;
    }

    /**
     * @param ModelGroupRoleInterface $modelGroupRole
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($modelGroupRole)
    {
        if (!$modelGroupRole instanceof ModelGroupRoleInterface) {
            throw new TransformerParameterTypeException();
        }
        $facade = $this->newFacade();
        $facade->modelId = $modelGroupRole->getId();
        $facade->type = $modelGroupRole->getType();
        $facade->name = $modelGroupRole->getRole();
        $facade->accessType = $modelGroupRole->getAccessType();

        return $facade;
    }

    /**
     * @param GroupInterface               $group
     * @param FacadeInterface              $modelGroupRoleFacade
     * @param ModelGroupRoleInterface|null $source
     *
     * @throws RoleNotFoundHttpException
     * @throws TransformerParameterTypeException
     *
     * @return ModelGroupRoleInterface
     */
    public function reverseTransformWithGroup(GroupInterface $group, FacadeInterface $modelGroupRoleFacade, $source = null)
    {
        if (!$source instanceof ModelGroupRoleInterface) {
            $source = new $this->modelGroupRoleClass();
        }
        if (!$this->collector->hasRole($modelGroupRoleFacade->name)) {
            throw new RoleNotFoundHttpException();
        }
        $source->setType($modelGroupRoleFacade->type);
        $source->setId($modelGroupRoleFacade->modelId);
        $source->setRole($modelGroupRoleFacade->name);
        $source->setAccessType($modelGroupRoleFacade->accessType);
        if (ModelGroupRoleInterface::ACCESS_INHERIT === $modelGroupRoleFacade->accessType) {
            $source->setGranted($this->isParentAccessGranted($group, $modelGroupRoleFacade));
        } else {
            $isGranted = (ModelGroupRoleInterface::ACCESS_GRANTED === $modelGroupRoleFacade->accessType) ? true : false;
            $source->setGranted($isGranted);
        }

        return $source;
    }

    /**
     * @param GroupInterface  $group
     * @param FacadeInterface $facade
     *
     * @return bool
     */
    protected function isParentAccessGranted(GroupInterface $group, FacadeInterface $facade)
    {
        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'model_group_role';
    }
}
