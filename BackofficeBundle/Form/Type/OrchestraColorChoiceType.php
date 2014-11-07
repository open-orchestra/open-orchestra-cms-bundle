<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class OrchestraColorChoiceType
 */
class OrchestraColorChoiceType extends AbstractType
{
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
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
            $choices[$color] = $this->translator->trans('php_orchestra_backoffice.form.status.color.'.$color);
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
