<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\EventSubscriber\SiteSubscriber;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class SiteSiteAliasType
 */
class SiteSiteAliasType extends AbstractType
{
    protected $siteRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('siteId', 'oo_site_choice', array(
            'label' => false,
            'attr' => array(
                'class' => 'to-tinyMce',
                'data-key' => 'site'
            ),
            'required' => false,
        ));
        if ($options['refresh']) {
            $builder->addEventSubscriber(
                new SiteSubscriber(
                    $this->siteRepository,
                    $options['attr']
            ));
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['refresh'] = $options['refresh'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'refresh' => false,
                'attr' => array()
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_site_site_alias';
    }
}
