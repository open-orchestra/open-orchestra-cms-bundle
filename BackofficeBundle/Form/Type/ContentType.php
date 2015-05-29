<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use OpenOrchestra\BackofficeBundle\EventSubscriber\ContentTypeSubscriber;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ContentType
 */
class ContentType extends AbstractType
{
    protected $contentTypeSubscriber;
    protected $contentClass;

    /**
     * @param ContentTypeSubscriber $contentTypeSubscriber
     * @param string                $contentClass
     */
    public function __construct(ContentTypeSubscriber $contentTypeSubscriber, $contentClass)
    {
        $this->contentTypeSubscriber = $contentTypeSubscriber;
        $this->contentClass = $contentClass;
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
                'label' => 'open_orchestra_backoffice.form.content_type.keywords',
                'required' => false
            ));

        $builder->addEventSubscriber($this->contentTypeSubscriber);
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_content';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->contentClass,
        ));
    }
}
