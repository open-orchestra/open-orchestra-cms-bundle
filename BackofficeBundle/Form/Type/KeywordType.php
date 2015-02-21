<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class KeywordType
 */
class KeywordType extends AbstractType
{
    /**
    * @param FormBuilderInterface $builder
    * @param array                $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text', array(
                'label' => 'open_orchestra_backoffice.form.keyword.label'
            )
        );

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
    * @return string
    */
    public function getName()
    {
        return 'keyword';
    }
}
