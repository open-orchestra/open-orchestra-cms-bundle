<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\TransformerInterface;

/**
 * Interface TransformerWithGroupInterface
 */
interface TransformerWithGroupInterface extends TransformerInterface
{
    /**
     * @param GroupInterface  $group
     * @param FacadeInterface $facade
     * @param mixed|null      $source
     *
     * @return mixed
     */
    public function reverseTransformWithGroup(GroupInterface $group, FacadeInterface $facade, $source = null);
}
