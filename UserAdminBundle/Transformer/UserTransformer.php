<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\UserBundle\Document\User;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\UserBundle\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class UserTransformer
 */
class UserTransformer extends AbstractTransformer
{
    protected $eventDispatcher;
    protected $multiLanguagesChoiceManager;
    protected $userRepository;

    /**
     * @param string                               $facadeClass
     * @param EventDispatcherInterface             $eventDispatcher
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param UserRepositoryInterface              $userRepository
     */
    public function __construct(
        $facadeClass,
        EventDispatcherInterface $eventDispatcher,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct($facadeClass);
        $this->eventDispatcher = $eventDispatcher;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = $this->newFacade();

        if (!is_null($mixed)) {
            $facade->id = $mixed->getId();
            $facade->username = $mixed->getUsername();
            $facade->roles = implode(',', $mixed->getRoles());

            $groups = $mixed->getGroups();
            $labels = array();
            foreach($groups as $group){
                if (!$group->isDeleted()) {
                    $labels[] = $this->multiLanguagesChoiceManager->choose($group->getLabels());
                }
            }

            $facade->groups = implode(',', $labels);
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
