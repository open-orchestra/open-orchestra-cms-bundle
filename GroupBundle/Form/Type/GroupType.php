<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\GroupBundle\Repository\GroupRepository;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GroupType
 */
class GroupType extends AbstractType
{
    protected $multiLanguagesChoiceManager;
    protected $groupRepository;

    /**
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param GroupRepository                      $groupRepository
     */
    public function __construct(
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        GroupRepository $groupRepository

    ) {
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->groupRepository = $groupRepository;
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
            'disabled' => false
        );
        $groupId = $view->vars['name'];
        $group = $this->groupRepository->find($groupId);

        if (!is_null($group)) {
            $view->vars['parameters'] = array(
                'groupName' => $this->multiLanguagesChoiceManager->choose($group->getLabels()),
                'siteName' => $group->getSite()->getName(),
                'disabled' => is_array($options['allowed_sites']) && !in_array($group->getSite()->getId(), $options['allowed_sites']),
            );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allowed_sites' => null,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_group';
    }
}
