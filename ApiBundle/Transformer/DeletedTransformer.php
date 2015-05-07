<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\DeletedFacade;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

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
