<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AreaFlexRowSubscriber;
use OpenOrchestra\BackofficeBundle\Form\Type\Component\ColumnLayoutRowType;
use OpenOrchestra\BackofficeBundle\Manager\AreaFlexManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AreaFlexType
 */
class AreaFlexType extends AbstractType
{
    protected $areaFlexManager;
    protected $translator;
    protected $areaClass;

    /**
     * @param string              $areaClass
     * @param AreaFlexManager     $areaFlexManager
     * @param TranslatorInterface $translator
     */
    public function __construct($areaClass, AreaFlexManager $areaFlexManager, TranslatorInterface $translator)
    {
        $this->areaClass = $areaClass;
        $this->areaFlexManager = $areaFlexManager;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('areaId', TextType::class);
        $builder->add('columnLayout', ColumnLayoutRowType::class, array(
            'label' => 'open_orchestra_backoffice.form.area_flex.column_layout.label',
            'mapped' => false,
            'attr' => array(
                'help_text' => 'open_orchestra_backoffice.form.area_flex.column_layout.helper',
            ),
        ));

        $builder->addEventSubscriber(new AreaFlexRowSubscriber($this->areaFlexManager));

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->areaClass,
            'attr' => array('data-title' => $this->translator->trans('open_orchestra_backoffice.form.area_flex.new_row_title'))
        ));
    }
}
