<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InternalLinkType
 */
class InternalLinkType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        if (!is_array($data) || !array_key_exists('siteId', $data)) {
            $data['siteId'] = $this->currentSiteManager->getSiteId();
        }

        $builder
        ->add('label', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.label',
        ))
        ->add('internalUrl', 'oo_internal_url', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.url',
            'required' => true,
            'data' => $data['siteId'],
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
        ));
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_internal_link";
    }
}
