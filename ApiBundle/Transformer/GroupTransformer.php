<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\GroupBundle\Event\GroupFacadeEvent;
use OpenOrchestra\GroupBundle\GroupFacadeEvents;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class GroupTransformer
 */
class GroupTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $eventDispatcher;
    protected $multiLanguagesChoiceManager;

    /**
     * @param string                               $facadeClass
     * @param AuthorizationCheckerInterface        $authorizationChecker
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param EventDispatcherInterface             $eventDispatcher
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        EventDispatcherInterface $eventDispatcher
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param GroupInterface $group
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($group)
    {
        if (!$group instanceof GroupInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $group->getId();
        $facade->name = $group->getName();
        $facade->label = $this->multiLanguagesChoiceManager->choose($group->getLabels());

        $facade = $this->addSite($facade, $group);
        $facade = $this->addRoles($facade, $group);
        $facade = $this->addLinks($facade, $group);

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
     *
     * @return FacadeInterface
     */
    protected function addLinks(FacadeInterface $facade, GroupInterface $group)
    {
        if ($this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $group)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_group_delete',
                array('groupId' => $group->getId())
            ));
        }

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $group)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_group_form',
                array('groupId' => $group->getId())
            ));
            $facade->addLink('_self_edit', $this->generateRoute(
                'open_orchestra_api_group_edit',
                array('groupId' => $group->getId())
            ));

            $this->eventDispatcher->dispatch(
                GroupFacadeEvents::POST_GROUP_TRANSFORMATION,
                new GroupFacadeEvent($group, $facade)
            );
        }

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, $group)) {
            $facade->addLink('_self_duplicate', $this->generateRoute('open_orchestra_api_group_duplicate', array(
                'groupId' => $group->getId(),
            )));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface     $facade
     * @param GroupInterface|null $group
     *
     * @return mixed
     * @throws
     */
    public function reverseTransform(FacadeInterface $facade, $group = null)
    {
        $this->eventDispatcher->dispatch(
            GroupFacadeEvents::POST_GROUP_REVERSE_TRANSFORMATION,
            new GroupFacadeEvent($group, $facade)
        );

        return $group;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
