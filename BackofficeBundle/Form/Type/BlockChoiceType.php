<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlockChoiceType
 */
class BlockChoiceType extends AbstractType
{
    /**
     * @param array               $choices
     * @param TranslatorInterface $translator
     */
    public function __construct(array $choices, TranslatorInterface $translator)
    {
        foreach ($choices as $choice) {
            $this->choices[$choice] = $translator->trans('open_orchestra_backoffice.block.' . $choice . '.title');
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->choices,
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_block';
    }
}
