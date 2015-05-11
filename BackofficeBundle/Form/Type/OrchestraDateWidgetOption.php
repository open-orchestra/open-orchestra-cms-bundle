<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraDateWidgetOption
 */
class OrchestraDateWidgetOption extends AbstractType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getChoices(),
        ));
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
        return 'orchestra_date_widget_option';
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        return array(
            'choice' => 'open_orchestra_backoffice.form.orchestra_fields.widget_type.choice',
            'text' => 'open_orchestra_backoffice.form.orchestra_fields.widget_type.text',
            'single_text' => 'open_orchestra_backoffice.form.orchestra_fields.widget_type.single_text',
        );
    }
}
