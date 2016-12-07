<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class ThemeCollectionTransformer
 */
class ThemeCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $themeCollection
     *
     * @return FacadeInterface
     */
    public function transform($themeCollection)
    {
        $facade = $this->newFacade();

        foreach ($themeCollection as $theme) {
            $facade->addTheme($this->getTransformer('theme')->transform($theme));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'theme_collection';
    }
}
