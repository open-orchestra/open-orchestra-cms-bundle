<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\UserBundle\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class UserTransformer
 */
class UserTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $eventDispatcher;
    protected $multiLanguagesChoiceManager;
    protected $userRepository;

    /**
     * @param string                               $facadeClass
     * @param AuthorizationCheckerInterface        $authorizationChecker
     * @param EventDispatcherInterface             $eventDispatcher
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param UserRepositoryInterface              $userRepository
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->eventDispatcher = $eventDispatcher;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserInterface $user
     *
     * @return FacadeInterface
     * @throws TransformerParameterTypeException
     */
    public function transform($user)
    {
        if (!$user instanceof UserInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $user->getId();
        $facade->username = $user->getUsername();
        $facade->email = $user->getEmail();
        $facade->roles = implode(',', $user->getRoles());

        $groups = $user->getGroups();
        $labels = array();
        foreach($groups as $group){
            if (!$group->isDeleted()) {
                $labels[] = $this->multiLanguagesChoiceManager->choose($group->getLabels());
            }
        }

        $facade->groups = implode(',', $labels);

        if ($this->hasGroup(CMSGroupContext::AUTHORIZATIONS)) {
            $canDelete = $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $user);
            $facade->addRight('can_delete', $canDelete);
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return UserInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        if (null !== $facade->id) {
            return $this->userRepository->find($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }

}
