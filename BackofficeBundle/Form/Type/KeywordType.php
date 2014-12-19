<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

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
                'label' => 'php_orchestra_backoffice.form.keyword.label'
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
