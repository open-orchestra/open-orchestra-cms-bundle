<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddContentSubmitButtonSubscriber;
use OpenOrchestra\BackofficeBundle\EventSubscriber\ContentTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContentType
 */
class ContentType extends AbstractType
{
    protected $contentTypeSubscriber;
    protected $contentClass;
    protected $buttonSubscriber;
    /**
     * @param ContentTypeSubscriber            $contentTypeSubscriber
     * @param string                           $contentClass
     * @param AddContentSubmitButtonSubscriber $buttonSubscriber
     */
    public function __construct(ContentTypeSubscriber $contentTypeSubscriber, $contentClass, AddContentSubmitButtonSubscriber $buttonSubscriber)
    {
        $this->contentTypeSubscriber = $contentTypeSubscriber;
        $this->contentClass = $contentClass;
        $this->buttonSubscriber = $buttonSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'open_orchestra_backoffice.form.content.name'
            ))
            ->add('keywords', 'orchestra_keywords', array(
                'label' => 'open_orchestra_backoffice.form.content.keywords',
                'required' => false
            ))
            ->add('linkedToSite', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.content.linked_to_site',
                'required' => false,
            ));

        $builder->addEventSubscriber($this->contentTypeSubscriber);
        $builder->addEventSubscriber($this->buttonSubscriber);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_content';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->contentClass,
        ));
    }
}
