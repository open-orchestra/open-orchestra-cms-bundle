<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\EventSubscriber\InternalUrlSubscriber;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InternalUrlType
 */
class InternalUrlType extends AbstractType
{
    protected $siteRepository;
    protected $nodeRepository;
    protected $currentSiteManager;
    protected $internalUrlClass;

    /**
     * @param SiteRepositoryInterface    $siteRepository
     * @param NodeRepositoryInterface    $nodeRepository
     * @param ContextBackOfficeInterface $currentSiteManager
     */
    public function __construct(
        SiteRepositoryInterface $siteRepository,
        NodeRepositoryInterface $nodeRepository,
        ContextBackOfficeInterface $currentSiteManager,
        $internalUrlClass
    ) {
        $this->siteRepository = $siteRepository;
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
        $this->internalUrlClass = $internalUrlClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('query', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.query',
            'required' => false,
        ))
        ->add('siteId', 'oo_site_choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.site',
            'attr' => array(
                'class' => 'patch-submit-change',
                'data-key' => 'site'
            ),
            'required' => true,
        ));
        $builder->addEventSubscriber(
            new InternalUrlSubscriber(
                $this->siteRepository,
                $this->nodeRepository,
                $options['attr']
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->internalUrlClass,
            'attr' => function(Options $options) {
                return array('class' => 'form-to-patch-and-send ' . $options['attr_class']);
            },
            'attr_class' => '',
        ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_internal_url';
    }
}
