<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class RoleStringTransformer
 */
class RoleStringTransformer extends AbstractTransformer
{
    /**
     * @param string $role
     * @param string $translation
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($role, $translation = null)
    {
        $facade = $this->newFacade();

        $facade->name = $role;
        $facade->description = $translation;

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'role_string';
    }
}
