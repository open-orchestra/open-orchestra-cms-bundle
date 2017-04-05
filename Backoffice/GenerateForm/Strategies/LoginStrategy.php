<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\UserBundle\DisplayBlock\LoginStrategy as BaseLoginStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class LoginStrategy
 */
class LoginStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseLoginStrategy::NAME === $block->getComponent();
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array(
            'maxAge' => 0
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'login';
    }
}
