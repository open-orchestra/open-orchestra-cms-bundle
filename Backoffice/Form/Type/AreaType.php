<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\Form\DataTransformer\HtmlElementTransformer;
use OpenOrchestra\Backoffice\EventSubscriber\AreaCollectionSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class TemplateAreaType
 */
class AreaType extends AbstractAreaContainerType
{
    protected $areaClass;
    protected $translator;

    /**
     * @param string              $areaClass
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(
        $areaClass,
        TranslatorInterface $translator,
        ObjectManager $objectManager
    ) {
        $this->areaClass = $areaClass;
        $this->translator = $translator;
        $this->setObjectManager($objectManager);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'text', array(
            'label' => 'open_orchestra_backoffice.form.area.label',
            'required' => false,
        ));
        $builder->add(
            $builder->create('areaId', 'text', array('label' => 'open_orchestra_backoffice.form.area.area_id'))
                ->addViewTransformer(new HtmlElementTransformer())
        );
        $builder->add(
            $builder->create('htmlClass', 'text', array(
                'required' => false,
                'label' => 'open_orchestra_backoffice.form.area.html_class'
            ))
                ->addViewTransformer(new HtmlElementTransformer())
        );
        $builder->add('boDirection', 'choice', array(
            'choices' => array('v' => 'vertical', 'h' => 'horizontal'),
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.area.bo_direction'
        ));

        $builder->addEventSubscriber(new AreaCollectionSubscriber($this->areaClass, $this->translator));
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->buildAreaListView($view, $form, $options);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->areaClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_area';
    }
}
