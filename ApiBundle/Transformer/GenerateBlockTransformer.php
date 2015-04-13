<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\GenerateBlockFacade;
use OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayManager;

/**
 * Class GenerateBlockTransformer
 */
class GenerateBlockTransformer extends AbstractTransformer
{

    protected $displayIconManager;

    /**
     * @param DisplayManager $displayIconManager
     */
    public function __construct(DisplayManager $displayIconManager)
    {
        $this->displayIconManager = $displayIconManager;
    }

    /**
     * @param string $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new GenerateBlockFacade();
        $facade->name = $mixed;
        $facade->icon = $this->displayIconManager->show($mixed);

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'generate_block';
    }
}
