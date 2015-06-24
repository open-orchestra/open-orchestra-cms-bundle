<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeHttpException;
use OpenOrchestra\ApiBundle\Facade\RoleFacade;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class RoleTransformer
 */
class RoleTransformer extends AbstractTransformer
{
    protected $translationChoiceManager;

    /**
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(TranslationChoiceManager $translationChoiceManager)
    {
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param RoleInterface $role
     *
     * @return RoleFacade
     *
     * @throws TransformerParameterTypeHttpException
     */
    public function transform($role)
    {
        if (!$role instanceof RoleInterface) {
            throw new TransformerParameterTypeHttpException();
        }

        $facade = new RoleFacade();

        $facade->id = $role->getId();
        $facade->name = $role->getName();
        $facade->description = $this->translationChoiceManager->choose($role->getDescriptions());
        $facade->fromStatus = $role->getFromStatus();
        $facade->toStatus = $role->getToStatus();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_role_show',
            array('roleId' => $role->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_role_delete',
            array('roleId' => $role->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_role_form',
            array('roleId' => $role->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'role';
    }
}
