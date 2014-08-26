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

    /**
     * @param ContentTypeRepository $contentTypeRepository
     */
    public function __construct(ContentTypeRepository $contentTypeRepository)
    {
        $this->contentTypeRepository = $contentTypeRepository;
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

        $builder->addEventSubscriber(new ContentTypeSubscriber($this->contentTypeRepository));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
