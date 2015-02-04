<?php

namespace PHPOrchestra\Backoffice\BlockParameter;

use PHPOrchestra\ModelInterface\Model\BlockInterface;

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
