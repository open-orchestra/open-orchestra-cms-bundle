<?php

namespace OpenOrchestra\ApiBundle\Transformer;

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
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        GroupRepositoryInterface $groupRepository
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param GroupInterface $group
     * @param integer        $nbrUsers
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($group, $nbrUsers = 0)
    {
        if (!$group instanceof GroupInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $group->getId();
        $facade->name = $group->getName();
        $facade->label = $this->multiLanguagesChoiceManager->choose($group->getLabels());
        $facade->nbrUsers = $nbrUsers;

        $facade = $this->addSite($facade, $group);
        $facade = $this->addRoles($facade, $group);
        $facade = $this->addRights($facade, $group, $nbrUsers);

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
            $facade->site = $this->getTransformer('site')->transform($site);
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param GroupInterface  $group
     * @param integer         $nbrUsers
     *
     * @return FacadeInterface
     */
    protected function addRights(FacadeInterface $facade, GroupInterface $group, $nbrUsers = 0)
    {

        $facade->addRight('can_edit', $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $group));
        $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $group) && $nbrUsers == 0);

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return GroupInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
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
