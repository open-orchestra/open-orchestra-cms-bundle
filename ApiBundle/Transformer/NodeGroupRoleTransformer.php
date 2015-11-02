<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\RoleNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade;
use OpenOrchestra\Backoffice\Collector\RoleCollector;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class NodeGroupRoleTransformer
 */
class NodeGroupRoleTransformer extends AbstractTransformer
{
    protected $nodeRoleGroupClass;
    protected $collector;

    /**
     * @param string        $nodeRoleGroupClass
     * @param RoleCollector $collector
     */
    public function __construct($nodeRoleGroupClass, RoleCollector $collector)
    {
        $this->nodeRoleGroupClass = $nodeRoleGroupClass;
        $this->collector = $collector;
    }

    /**
     * @param NodeGroupRoleInterface $nodeGroupRole
     *
     * @return NodeGroupRoleFacade
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($nodeGroupRole)
    {
        if (!$nodeGroupRole instanceof NodeGroupRoleInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new NodeGroupRoleFacade();

        $facade->node = $nodeGroupRole->getNodeId();
        $facade->name = $nodeGroupRole->getRole();
        $facade->isGranted = $nodeGroupRole->isGranted();

        return $facade;
    }

    /**
     * @param FacadeInterface|NodeGroupRoleFacade $facade
     * @param NodeGroupRoleInterface|null         $source
     *
     * @throws RoleNotFoundHttpException
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (!$source instanceof NodeGroupRoleInterface) {
            $source = new $this->nodeRoleGroupClass();
        }

        if (!$this->collector->hasRole($facade->name)) {
            throw new RoleNotFoundHttpException();
        }

        $source->setNodeId($facade->node);
        $source->setRole($facade->name);
        $source->setGranted($facade->isGranted);

        return $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_group_role';
    }
}
