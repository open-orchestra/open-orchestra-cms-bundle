<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeTreeLeafTransformer
 */
class NodeTreeLeafTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param array $node
     *
     * @return FacadeInterface
     */
    public function transform($node)
    {
        $facade = $this->newFacade();

        $facade->nodeId = $node['nodeId'];
        $facade->name = $node['name'];
        $facade->language = $node['language'];
        $facade->version = $node['version'];
        $facade->siteId = $node['siteId'];
        $facade->order = $node['order'];
        $facade->status = $this->getTransformer('status_node_tree')->transform($node['status']);
        $facade->nodeType = $node['nodeType'];

        $facade->addRight('can_create', (
            $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, $node) &&
            NodeInterface::TYPE_DEFAULT === $node['nodeType']
        ));
        $facade->addRight('can_read', $this->authorizationChecker->isGranted(ContributionActionInterface::READ, $node));
        $facade->addRight('can_edit', $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $node));

        return $facade;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'node_tree_leaf';
    }
}
