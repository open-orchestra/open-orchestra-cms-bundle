<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Manager\BlockConfigurationManager;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BlockComponentTransformer
 */
class BlockComponentTransformer extends AbstractTransformer
{
    protected $blockConfigurationManager;

    /**
     * @param string                    $facadeClass
     * @param BlockConfigurationManager $blockConfigurationManager
     */
    public function __construct(
        $facadeClass,
        BlockConfigurationManager $blockConfigurationManager
    ) {
        parent::__construct($facadeClass);
        $this->blockConfigurationManager = $blockConfigurationManager;
    }

    /**
     * @param string $blockComponent
     *
     * @return FacadeInterface
     */
    public function transform($blockComponent)
    {
        $facade = $this->newFacade();

        $facade->component = $blockComponent;
        $facade->name = $this->blockConfigurationManager->getBlockComponentName($blockComponent);
        $facade->category = $this->blockConfigurationManager->getBlockCategory($blockComponent);
        $facade->description = $this->blockConfigurationManager->getBlockComponentDescription($blockComponent);

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'block_component';
    }
}
