<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\DeletedFacade;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class DeletedTransformer
 */
class DeletedTransformer extends AbstractTransformer
{
    /**
     * @param NodeInterface|ContentInterface $mixed

     * @return DeletedFacade|void
     */
    public function transform($mixed)
    {
        if ($mixed instanceof NodeInterface) {
            return $this->getTransformer('node')->transform($mixed);
        }
        if ($mixed instanceof ContentInterface) {
            return $this->getTransformer('content')->transform($mixed);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'deleted';
    }
}
