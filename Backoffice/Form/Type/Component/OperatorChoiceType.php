<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class OperatorChoiceType
 */
class OperatorChoiceType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'empty_data' => ContentRepositoryInterface::CHOICE_AND,
                'constraints' => new NotBlank(),
                'choices' => array(
                    ContentRepositoryInterface::CHOICE_AND => 'open_orchestra_backoffice.form.content_list.choice_type_and',
                    ContentRepositoryInterface::CHOICE_OR => 'open_orchestra_backoffice.form.content_list.choice_type_or',
                ),
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
        return 'oo_operator_choice';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
}
