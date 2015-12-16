<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class RoleTransformer
 */
class RoleTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $translationChoiceManager;

    /**
     * @param string $facadeClass
     * @param TranslationChoiceManager $translationChoiceManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        TranslationChoiceManager $translationChoiceManager,
        AuthorizationCheckerInterface $authorizationChecker
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param RoleInterface $role
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($role)
    {
        if (!$role instanceof RoleInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $role->getId();
        $facade->name = $role->getName();
        $facade->description = $this->translationChoiceManager->choose($role->getDescriptions());
        $facade->fromStatus = $role->getFromStatus();
        $facade->toStatus = $role->getToStatus();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_role_show',
            array('roleId' => $role->getId())
        ));
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_ROLE)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_role_delete',
                array('roleId' => $role->getId())
            ));
        }
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_ROLE)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_role_form',
                array('roleId' => $role->getId())
            ));
        }

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
