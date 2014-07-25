<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockManager;
use PHPOrchestra\ModelBundle\Model\BlockInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $displayBlockManager;

    /**
     * @param DisplayBlockManager $displayBlockManager
     */
    public function __construct(DisplayBlockManager $displayBlockManager)
    {
        $this->displayBlockManager = $displayBlockManager;
    }

    /**
     * @param BlockInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new BlockFacade();

        $facade->method = 'generate';
        $facade->component = $mixed->getComponent();

        foreach ($mixed->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $facade->addAttribute($key, json_encode($attribute));
            } else {
                $facade->addAttribute($key, $attribute);
            }
        }

        $html = $this->displayBlockManager->showBack($mixed)->getContent();
        $facade->uiModel = $this->getTransformer('ui_model')->transform(array(
            'label' => $mixed->getComponent(),
            'html' => $html
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'block';
    }
}
