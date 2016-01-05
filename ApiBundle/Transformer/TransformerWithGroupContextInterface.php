<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\TransformerInterface;

/**
 * Interface TransformerWithGroupContextInterface
 */
interface TransformerWithGroupContextInterface extends TransformerInterface
{
    /**
     * @param GroupInterface  $group
     * @param FacadeInterface $facade
     * @param mixed|null      $source
     *
     * @return mixed
     */
    public function reverseTransformWithContext(GroupInterface $group, FacadeInterface $facade, $source = null);
}
