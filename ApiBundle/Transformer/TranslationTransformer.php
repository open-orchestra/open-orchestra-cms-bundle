<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class TranslationTransformer
 */
class TranslationTransformer extends AbstractTransformer
{
    /**
     * @param array $catalog
     * @param string $locale
     * 
     * @return FacadeInterface
     */
    public function transform($catalog, $locale = null)
    {
        $facade = $this->newFacade();

        $facade->locale = $locale;
        $facade->catalog = $catalog;

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translation';
    }
}
