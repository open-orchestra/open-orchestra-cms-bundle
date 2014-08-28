<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\ContentTypeSubscriber;
use PHPOrchestra\BackofficeBundle\Form\DataTransformer\ContentAttributesTransformer;
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

    /**
     * @param ContentTypeRepository $contentTypeRepository
     * @param string                $contentClass
     * @param string                $contentAttributClass
     */
    public function __construct(ContentTypeRepository $contentTypeRepository, $contentClass, $contentAttributClass)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentClass = $contentClass;
        $this->contentAttributClass = $contentAttributClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('status', 'orchestra_status')
            ->add('language', 'orchestra_language')
            ->add('contentType', 'document', array(
                'class' => 'PHPOrchestra\ModelBundle\Document\ContentType'
            ));

        $builder->addEventSubscriber(new ContentTypeSubscriber($this->contentTypeRepository, $this->contentAttributClass));
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
