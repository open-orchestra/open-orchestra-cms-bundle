<?php

namespace OpenOrchestra\GroupBundle\Transformer;

use OpenOrchestra\Backoffice\BusinessRules\BusinessRulesManager;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface;

/**
 * Class GroupTransformer
 */
class GroupTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $multiLanguagesChoiceManager;

    /**
     * @param string                               $facadeClass
     * @param AuthorizationCheckerInterface        $authorizationChecker
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param GroupRepositoryInterface             $groupRepository
     * @param BusinessRulesManager                 $businessRulesManager
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        GroupRepositoryInterface $groupRepository,
        BusinessRulesManager $businessRulesManager
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->groupRepository = $groupRepository;
        $this->businessRulesManager = $businessRulesManager;
    }

    /**
     * @param GroupInterface $group
     * @param array          $params
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($group, array $params = array())
    {
        if (!$group instanceof GroupInterface) {
            throw new TransformerParameterTypeException();
        }
        $nbrGroupsUsers = array_key_exists('nbrGroupsUsers', $params) ? $params['nbrGroupsUsers'] : array();

        $facade = $this->newFacade();

        $facade->id = $group->getId();
        $facade->name = $group->getName();
        $facade->label = $this->multiLanguagesChoiceManager->choose($group->getLabels());
        $facade->nbrUsers = array_key_exists($group->getId(), $nbrGroupsUsers) ? $nbrGroupsUsers[$group->getId()] : 0;

        $facade = $this->addSite($facade, $group);
        $facade = $this->addRoles($facade, $group);
        $facade = $this->addRights($facade, $group, $nbrGroupsUsers);

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param GroupInterface  $group
     *
     * @return FacadeInterface
     */
    protected function addRoles(FacadeInterface $facade, GroupInterface $group)
    {
        if ($this->hasGroup(CMSGroupContext::GROUP_ROLES)) {
            foreach ($group->getRoles() as $role) {
                $facade->addRole($role);
            }
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param GroupInterface  $group
     *
     * @return FacadeInterface
     */
    protected function addSite(FacadeInterface $facade, GroupInterface $group)
    {
        if ($this->hasGroup(CMSGroupContext::SITE) && $site = $group->getSite()) {
            $facade->site = $this->getContext()->transform('site', $site);
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param GroupInterface  $group
     * @param array           $nbrUsers
     *
     * @return FacadeInterface
     */
    protected function addRights(FacadeInterface $facade, GroupInterface $group, array $nbrGroupsUsers)
    {
        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $facade->addRight('can_delete',
                $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $group) &&
                $this->businessRulesManager->isGranted(BusinessActionInterface::DELETE, $group, $nbrGroupsUsers)
            );
            $facade->addRight('can_duplicate', $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, GroupInterface::ENTITY_TYPE));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return GroupInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        if (null !== $facade->id) {
            return $this->groupRepository->find($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
