<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\NodeFacade;
use PHPOrchestra\BaseBundle\Manager\EncryptionManager;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Model\SiteInterface;
use PHPOrchestra\ModelBundle\Repository\SiteRepository;
use PHPOrchestra\ModelBundle\Repository\StatusRepository;

/**
 * Class NodeTransformer
 */
class NodeTransformer extends AbstractTransformer
{
    protected $encrypter;
    protected $siteRepository;
    protected $statusRepository;

    /**
     * @param EncryptionManager $encrypter
     * @param SiteRepository    $siteRepository
     * @param StatusRepository  $statusRepository
     */
    public function __construct(EncryptionManager $encrypter, SiteRepository $siteRepository, StatusRepository $statusRepository)
    {
        $this->encrypter = $encrypter;
        $this->siteRepository = $siteRepository;
        $this->statusRepository = $statusRepository;
    }

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

        $facade->addLink('_self_form', $this->generateRoute('php_orchestra_backoffice_node_form', array(
            'id' => $mixed->getId(),
        )));

        $facade->addLink('_self_duplicate', $this->generateRoute('php_orchestra_api_node_duplicate', array(
            'nodeId' => $mixed->getNodeId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_version', $this->generateRoute('php_orchestra_api_node_list_version', array(
            'nodeId' => $mixed->getNodeId(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_node_delete', array(
            'nodeId' => $mixed->getNodeId()
        )));

        $facade->addLink('_self_without_language', $this->generateRoute('php_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId()
        )));

        $facade->addLink('_self', $this->generateRoute('php_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId(),
            'version' => $mixed->getVersion(),
            'language' => $mixed->getLanguage(),
        )));

        $facade->addLink('_site', $this->generateRoute('php_orchestra_api_site_show', array(
            'siteId' => $mixed->getSiteId(),
        )));

        if ($site = $this->siteRepository->findOneBySiteId($mixed->getSiteId())) {
            $facade->addLink('_self_preview', 'http://' . $site->getAlias() . '/preview?token=' . $this->encrypter->encrypt($mixed->getId()));
        }

        $facade->addLink('_status_list', $this->generateRoute('php_orchestra_api_list_status_node', array(
            'nodeMongoId' => $mixed->getId()
        )));

        $facade->addLink('_self_status_change', $this->generateRoute('php_orchestra_api_node_update', array(
            'nodeMongoId' => $mixed->getId()
        )));

        $facade->addLink('_existing_block', $this->generateRoute('php_orchestra_backoffice_block_exsting', array(
            'language' => $mixed->getLanguage(),
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
        $facade->status = $this->getTransformer('status')->transform($mixed->getStatus());

        $facade->addLink('_self', $this->generateRoute('php_orchestra_api_node_show', array(
            'nodeId' => $mixed->getNodeId(),
            'version' => $mixed->getVersion(),
            'language' => $mixed->getLanguage(),
        )));

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param mixed           $node
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if ($source) {
            if ($facade->statusId) {
                $newStatus = $this->statusRepository->find($facade->statusId);
                if ($newStatus) {
                    $source->setStatus($newStatus);
                }
            }
        }

        return $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node';
    }

}
