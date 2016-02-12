<?php

namespace OpenOrchestra\Backoffice\BlockParameter\Strategies;

use OpenOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContactStrategy as BaseContactStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class ContactStrategy
 */
class ContactStrategy implements BlockParameterInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseContactStrategy::NAME == $block->getComponent();
    }

    /**
     * @return array
     */
    public function getBlockParameter()
    {
        return array('post_data', 'request.aliasId');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }

}
