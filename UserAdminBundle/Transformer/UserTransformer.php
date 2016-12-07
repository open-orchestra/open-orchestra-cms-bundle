<?php

namespace OpenOrchestra\UserAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\UserBundle\Document\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class UserTransformer
 */
class UserTransformer extends AbstractTransformer
{
    protected $eventDispatcher;
    protected $multiLanguagesChoiceManager;

    /**
     * @param string                               $facadeClass
     * @param EventDispatcherInterface             $eventDispatcher
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     */
    public function __construct(
        $facadeClass,
        EventDispatcherInterface $eventDispatcher,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
    ) {
        parent::__construct($facadeClass);
        $this->eventDispatcher = $eventDispatcher;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
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
                $labels[] = $this->multiLanguagesChoiceManager->choose($group->getLabels());
            }

            $facade->groups = implode(',', $labels);
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }

}
