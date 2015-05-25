<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class OrchestraColorChoiceType
 *
 * @deprecated use OrchestraChoiceType instead, will be removed in 0.2.5
 */
class OrchestraColorChoiceType extends AbstractType
{
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->getChoices(),
            )
        );
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $colors = array('red', 'orange', 'green');

        $choices = array();
        foreach ($colors as $color) {
            $choices[$color] = $this->translator->trans('open_orchestra_backoffice.form.status.color.'.$color);
        }

        return $choices;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'orchestra_color_choice';
    }
}
