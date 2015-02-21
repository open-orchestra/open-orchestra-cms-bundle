<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MediaMetaType
 */
class MediaMetaType extends AbstractType
{
    protected $mediaClass;

    /**
     * @param string $mediaClass
     */
    public function __construct($mediaClass)
    {
        $this->mediaClass = $mediaClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', null, array(
            'label' => 'open_orchestra_backoffice.form.media.meta.title',
            'required' => false,
        ));
        $builder->add('alt', null, array(
            'label' => 'open_orchestra_backoffice.form.media.meta.alt',
            'required' => false,
        ));
        $builder->add('copyright', null, array(
            'label' => 'open_orchestra_backoffice.form.media.meta.copyright',
            'required' => false,
        ));
        $builder->add('comment', 'textarea', array(
            'label' => 'open_orchestra_backoffice.form.media.meta.comment',
            'required' => false,
        ));
        $builder->add('keywords', 'orchestra_keywords', array(
            'label' => 'open_orchestra_backoffice.form.media.meta.keywords',
            'required' => false
        ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->mediaClass,
        ));
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return 'media_meta';
    }
}
