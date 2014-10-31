<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\NodeFacade;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelBundle\Model\NodeInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractTransformer
{
    /**
     * @param NodeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new NodeFacade();

        foreach ($mixed->getAreas() as $area) {
            $facade->addArea($this->getTransformer('area')->transform($area, $mixed));
        }

        $facade->nodeId = $mixed->getNodeId();
        $facade->name = $mixed->getName();
        $facade->siteId = $mixed->getSiteId();
        $facade->deleted = $mixed->getDeleted();
        $facade->templateId = $mixed->getTemplateId();
        $facade->nodeType = $mixed->getNodeType();
        $facade->parentId = $mixed->getParentId();
        $facade->path = $mixed->getPath();
        $facade->alias = $mixed->getAlias();
        $facade->language = $mixed->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($mixed->getStatus());
        $facade->theme = $mixed->getTheme();
        $facade->version = $mixed->getVersion();
        $facade->createdBy = $mixed->getCreatedBy();
        $facade->updatedBy = $mixed->getUpdatedBy();
        $facade->createdAt = $mixed->getCreatedAt();
        $facade->updatedAt = $mixed->getUpdatedAt();

        $facade->addLink('_self_form', $this->generateRoute('php_orchestra_backoffice_node_form',
            array('nodeId' => $mixed->getNodeId())
        ));

        $facade->addLink('_self_duplicate', $this->generateRoute('php_orchestra_api_node_duplicate',
            array('nodeId' => $mixed->getNodeId())
        ));

        $facade->addLink('_self_version', $this->generateRoute('php_orchestra_api_node_list_version', array(
            'nodeId' => $mixed->getNodeId()
        )));

        $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_node_delete', array(
            'nodeId' => $mixed->getNodeId()
        )));

        return $facade;
    }

    /**
     * @param NodeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transformVersion($mixed)
    {
        $facade = new NodeFacade();

        $facade->nodeId = $mixed->getNodeId();
        $facade->name = $mixed->getName();
        $facade->version = $mixed->getVersion();
        $facade->createdBy = $mixed->getCreatedBy();
        $facade->updatedBy = $mixed->getUpdatedBy();
        $facade->createdAt = $mixed->getCreatedAt();
        $facade->updatedAt = $mixed->getUpdatedAt();

        $facade->addLink('_self', $this->generateRoute('php_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId(),
            'version' => $mixed->getVersion(),
        )));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node';
    }

}
