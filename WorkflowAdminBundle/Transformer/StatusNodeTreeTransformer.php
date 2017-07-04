<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class StatusNodeTreeTransformer
 */
class StatusNodeTreeTransformer extends AbstractTransformer
{
    /**
     * @param array $status
     * @param array $params
     *
     * @return FacadeInterface
     */
    public function transform($status, array $params = array())
    {
        $facade = $this->newFacade();

        $facade->codeColor = $status['displayColor'];

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'status_node_tree';
    }
}
