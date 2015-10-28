<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\GroupFacade;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class GroupTransformer
 */
class GroupTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $translationChoiceManager;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TranslationChoiceManager $translationChoiceManager
    ){
        parent::__construct($authorizationChecker);
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param GroupInterface $group
     *
     * @return GroupFacade
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($group)
    {
        if (!$group instanceof GroupInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new GroupFacade();

        $facade->id = $group->getId();
        $facade->name = $group->getName();
        $facade->label = $this->translationChoiceManager->choose($group->getLabels());

        foreach ($group->getRoles() as $role) {
            $facade->addRole($role);
        }
        if ($site = $group->getSite()) {
            $facade->site = $this->getTransformer('site')->transform($site);
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_GROUP)) {
            $facade->addLink('_self', $this->generateRoute(
                'open_orchestra_api_group_show',
                array('groupId' => $group->getId())
            ));
        }
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_GROUP)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_group_delete',
                array('groupId' => $group->getId())
            ));
        }
        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_GROUP)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_group_form',
                array('groupId' => $group->getId())
            ));
        }
        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
