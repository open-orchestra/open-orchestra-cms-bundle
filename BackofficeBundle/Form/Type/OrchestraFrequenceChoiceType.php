<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraFrequenceChoiceType
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
            'always' => 'php_orchestra_backoffice.form.node.changefreq.always',
            'hourly' => 'php_orchestra_backoffice.form.node.changefreq.hourly',
            'daily' => 'php_orchestra_backoffice.form.node.changefreq.daily',
            'weekly' => 'php_orchestra_backoffice.form.node.changefreq.weekly',
            'monthly' => 'php_orchestra_backoffice.form.node.changefreq.monthly',
            'yearly' => 'php_orchestra_backoffice.form.node.changefreq.yearly',
            'never' => 'php_orchestra_backoffice.form.node.changefreq.never'
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
