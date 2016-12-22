<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\GroupBundle\Repository\GroupRepository;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class GroupElementType
 */
class GroupElementType extends AbstractType
{
    protected $multiLanguagesChoiceManager;
    protected $groupRepository;
    protected $authorizationChecker;

    /**
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param GroupRepository                      $groupRepository
     * @param AuthorizationCheckerInterface        $authorizationChecker
     */
    public function __construct(
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        GroupRepository $groupRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->groupRepository = $groupRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groupId = '__id__';
        if (!is_null($options['property_path'])) {
            $groupId = preg_replace('/^\[(.*)\]$/', '$1', $options['property_path']);
        }

        $builder->add('group', 'radio', array(
            'value' => $groupId,
            'data' => true,
        ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['checked'] = true;
        $view->vars['parameters'] = array(
            'groupName' => '__label__',
            'siteName' => '__site.name__',
            'deleted' => false,
            'disabled' => false,
        );
        $groupId = $view->vars['name'];
        $group = $this->groupRepository->find($groupId);

        if (!is_null($group)) {
            $view->vars['parameters'] = array(
                'groupName' => $this->multiLanguagesChoiceManager->choose($group->getLabels()),
                'siteName' => $group->getSite()->getName(),
                'deleted' => $group->isDeleted(),
                'disabled' => !$this->authorizationChecker->isGranted(ContributionActionInterface::READ, $group),
            );
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_group_element';
    }
}
