<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\GroupBundle\Form\DataTransformer\GroupListToArrayTransformer;

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
                'attr' => array('data-site' => $group->getSite()->getName()),
            ));
        }
        $builder->add('__prototype-id__', 'radio', array(
            'label' => '__prototype-label__',
            'attr' => array(
                'data-site' => '__prototype-site.name__',
                'type' => 'prototype'),
            'mapped' => false,
        ));
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
