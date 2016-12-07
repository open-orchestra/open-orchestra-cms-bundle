<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\AreaTransformerHttpException;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class AreaTransformer
 */
class AreaTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $nodeRepository;
    protected $areaManager;

    /**
     * @param string                        $facadeClass
     * @param NodeRepositoryInterface       $nodeRepository
     * @param AreaManager                   $areaManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        NodeRepositoryInterface $nodeRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param AreaInterface      $area
     * @param NodeInterface|null $node
     * @param string|null        $areaId
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     * @throws AreaTransformerHttpException
     */
    public function transform($area, NodeInterface $node = null, $areaId = null)
    {
        $facade = $this->newFacade();

        if (!$area instanceof AreaInterface) {
            throw new TransformerParameterTypeException();
        }

        if (!$node instanceof NodeInterface) {
            throw new AreaTransformerHttpException();
        }

        $facade->editable = $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $node);

        foreach ($area->getBlocks() as $blockPosition => $block) {
            $facade->addBlock($this->getTransformer('block')->transform($block, $blockPosition));
        }

        if ($facade->editable) {
            $this->addLinksFromNode($facade, $node, $area, $areaId);
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param NodeInterface   $node
     * @param AreaInterface   $area
     * @param string          $areaId
     */
    protected function addLinksFromNode(
        FacadeInterface $facade,
        NodeInterface $node,
        AreaInterface $area,
        $areaId
    ) {
        $facade->addLink('_block_list', $this->generateRoute('open_orchestra_api_block_list_with_transverse', array('language' => $node->getLanguage())));

        $facade->addLink('_self_update_block_position', $this->generateRoute('open_orchestra_api_area_update_block_position', array(
            'nodeId' => $node->getNodeId(),
            'language' => $node->getLanguage(),
            'version' => $node->getVersion(),
            'siteId' => $node->getSiteId(),
        )));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area';
    }
}
