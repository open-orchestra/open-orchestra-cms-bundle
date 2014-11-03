<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BaseBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FolderType
 */
class FolderType extends AbstractType
{
    protected $folderClass;

    /**
     * @param string $folderClass
     */
    public function __construct($folderClass)
    {
        $this->folderClass = $folderClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'php_orchestra_backoffice.form.folder.name'
            ))
            ->add('sites', 'orchestra_site', array(
                'label' => 'php_orchestra_backoffice.form.folder.site',
                'multiple' => true
            ));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->folderClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'folder';
    }

}
