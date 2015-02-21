<?php

namespace OpenOrchestra\Backoffice\BlockParameter;

use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Interface BlockParameterInterface
 */
interface BlockParameterInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block);

    /**
     * @return array
     */
    public function getBlockParameter();

    /**
     * @return string
     */
    public function getName();
}
