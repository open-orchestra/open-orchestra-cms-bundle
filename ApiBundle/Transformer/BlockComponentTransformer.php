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
    protected $translator;

    /**
     * @param string                    $facadeClass
     * @param BlockConfigurationManager $blockConfigurationManager
     * @param TranslatorInterface       $translator
     */
    public function __construct(
        $facadeClass,
        BlockConfigurationManager $blockConfigurationManager,
        TranslatorInterface $translator
    ) {
        parent::__construct($facadeClass);
        $this->blockConfigurationManager = $blockConfigurationManager;
        $this->translator = $translator;
    }

    /**
     * @param string $blockComponent
     * @param array  $params
     *
     * @return FacadeInterface
     */
    public function transform($blockComponent, array $params = array())
    {
        $facade = $this->newFacade();

        $facade->component = $blockComponent;
        $facade->name = $this->blockConfigurationManager->getBlockComponentName($blockComponent);
        $categoryKey = $this->blockConfigurationManager->getBlockCategory($blockComponent);
        $facade->category = array(
            'label' => $this->translator->trans($categoryKey),
            'key' => $categoryKey
        );
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
