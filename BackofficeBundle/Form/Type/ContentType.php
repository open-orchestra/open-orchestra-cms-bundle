<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\ContentTypeSubscriber;
use PHPOrchestra\ModelBundle\Repository\ContentTypeRepository;
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

    /**
     * @param ContentTypeRepository    $contentTypeRepository
     * @param string                   $contentClass
     * @param string                   $contentAttributClass
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(
        ContentTypeRepository $contentTypeRepository,
        $contentClass,
        $contentAttributClass,
        TranslationChoiceManager $translationChoiceManager
    )
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentClass = $contentClass;
        $this->contentAttributClass = $contentAttributClass;
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'php_orchestra_backoffice.form.content.name'
            ))
            ->add('status', 'orchestra_status', array(
                'label' => 'php_orchestra_backoffice.form.content.status'
            ))
            ->add('language', 'orchestra_language', array(
                'label' => 'php_orchestra_backoffice.form.content.language'
            ))
            ->add('contentType', 'document', array(
                'class' => 'PHPOrchestra\ModelBundle\Document\ContentType',
                'label' => 'php_orchestra_backoffice.form.content.content_type'
            ));

        $builder->addEventSubscriber(new ContentTypeSubscriber(
            $this->contentTypeRepository,
            $this->contentAttributClass,
            $this->translationChoiceManager
        ));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
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
