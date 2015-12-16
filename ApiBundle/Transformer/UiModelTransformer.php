<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class UiModelTransformer
 */
class UiModelTransformer extends AbstractTransformer
{
    /**
     * @param array $uiModel
     *
     * @return FacadeInterface
     */
    public function transform($uiModel)
    {
        $facade = $this->newFacade();

        if (array_key_exists('label', $uiModel)) {
            $facade->label = $uiModel['label'];
        }

        if (array_key_exists('class', $uiModel)) {
            $facade->class = $uiModel['class'];
        }

        if (array_key_exists('id', $uiModel)) {
            $facade->id = $uiModel['id'];
        }

        if (array_key_exists('html', $uiModel)) {
            $facade->html = $uiModel['html'];
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ui_model';
    }
}
