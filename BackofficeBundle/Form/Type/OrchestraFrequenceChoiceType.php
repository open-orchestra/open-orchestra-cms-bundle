<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraFrequenceChoiceType
 *
 * @deprecated we use the OrchestraChoiceType instead, will be removed in 0.2.5
 */
class OrchestraFrequenceChoiceType extends AbstractType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->getChoices()
            )
        );
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        return array(
            'always' => 'open_orchestra_backoffice.form.changefreq.always',
            'hourly' => 'open_orchestra_backoffice.form.changefreq.hourly',
            'daily' => 'open_orchestra_backoffice.form.changefreq.daily',
            'weekly' => 'open_orchestra_backoffice.form.changefreq.weekly',
            'monthly' => 'open_orchestra_backoffice.form.changefreq.monthly',
            'yearly' => 'open_orchestra_backoffice.form.changefreq.yearly',
            'never' => 'open_orchestra_backoffice.form.changefreq.never'
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'orchestra_frequence_choice';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }
}
