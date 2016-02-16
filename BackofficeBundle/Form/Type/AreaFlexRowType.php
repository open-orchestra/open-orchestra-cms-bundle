<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AreaFlexRowSubscriber;
use OpenOrchestra\BackofficeBundle\Form\Type\Component\ColumnLayoutRowType;
use OpenOrchestra\BackofficeBundle\Manager\AreaFlexManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AreaFlexType
 */
class AreaFlexRowType extends AbstractAreaFlexType
{
    protected $areaFlexManager;

    /**
     * @param string              $areaClass
     * @param TranslatorInterface $translator
     * @param AreaFlexManager     $areaFlexManager
     */
    public function __construct($areaClass, TranslatorInterface $translator, AreaFlexManager $areaFlexManager)
    {
        parent::__construct($areaClass, $translator);
        $this->areaFlexManager = $areaFlexManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('columnLayout', ColumnLayoutRowType::class, array(
            'label' => 'open_orchestra_backoffice.form.area_flex.column_layout.label',
            'mapped' => false,
            'attr' => array(
                'help_text' => 'open_orchestra_backoffice.form.area_flex.column_layout.helper',
            ),
        ));

        $builder->addEventSubscriber(new AreaFlexRowSubscriber($this->areaFlexManager));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault(
            'attr',
            array('data-title' => $this->translator->trans('open_orchestra_backoffice.form.area_flex.new_row_title'))
        );
    }
}
