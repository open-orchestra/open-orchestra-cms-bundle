<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\RoleFacade;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
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
     * @return RoleFacade
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($role, $translation = null)
    {
        $facade = new RoleFacade();

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
