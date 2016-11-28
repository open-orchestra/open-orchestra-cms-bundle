<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\GroupBundle\Form\DataTransformer\GroupListToArrayTransformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class GroupListType
 */
class GroupListType extends AbstractType
{
    protected $multiLanguagesChoiceManager;
    protected $groupListToArrayTransformer;

    /**
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param GroupListToArrayTransformer          $groupListToArrayTransformer
     */
    public function __construct(
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        GroupListToArrayTransformer $groupListToArrayTransformer
    ) {
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->groupListToArrayTransformer = $groupListToArrayTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->groupListToArrayTransformer);

        foreach ($options['groups'] as $group) {
            $builder->add($group->getId(), 'radio', array(
                'label' => $this->multiLanguagesChoiceManager->choose($group->getLabels()),
            ));
        }
    }
    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //var_dump($options['groups']);
        var_dump(($view->vars['form']));
//        $view->vars['refresh'] = $options['refresh'];
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'groups' => array(),
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_group_list';
    }
}
