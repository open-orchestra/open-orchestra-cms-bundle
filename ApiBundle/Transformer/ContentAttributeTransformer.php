<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;

/**
 * Class ContentAttributeTransformer
 */
class ContentAttributeTransformer extends AbstractTransformer
{
    /**
     * @param ContentAttributeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ContentAttributeFacade();

        $facade->name = $mixed->getName();
        $facade->value = $mixed->getValue();

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_attribute';
    }
}
