<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\NodeGroupRoleFacade;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class NodeGroupRoleTransformer
 */
class NodeGroupRoleTransformer extends AbstractTransformer
{
    protected $nodeRoleGroupClass;

    /**
     * @param string $nodeRoleGroupClass
     */
    public function __construct($nodeRoleGroupClass)
    {
        $this->nodeRoleGroupClass = $nodeRoleGroupClass;
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
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (!$source instanceof NodeGroupRoleInterface) {
            $source = new $this->nodeRoleGroupClass();
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
