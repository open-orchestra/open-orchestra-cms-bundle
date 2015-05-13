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
    protected $contentTypeRepository;
    protected $contentClass;
    protected $contentAttributClass;
    protected $translationChoiceManager;
    protected $fieldTypesConfiguration;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param string                         $contentClass
     * @param string                         $contentAttributClass
     * @param TranslationChoiceManager       $translationChoiceManager
     * @param array                          $fieldTypesConfiguration
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        $contentClass,
        $contentAttributClass,
        TranslationChoiceManager $translationChoiceManager,
        $fieldTypesConfiguration
    )
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentClass = $contentClass;
        $this->contentAttributClass = $contentAttributClass;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->fieldTypesConfiguration = $fieldTypesConfiguration;
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

        $builder->addEventSubscriber(new ContentTypeSubscriber(
            $this->contentTypeRepository,
            $this->contentAttributClass,
            $this->translationChoiceManager,
            $this->fieldTypesConfiguration
        ));
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
