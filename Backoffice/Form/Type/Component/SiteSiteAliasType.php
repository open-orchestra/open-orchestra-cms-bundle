<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\EventSubscriber\SiteSubscriber;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SiteSiteAliasType
 */
class SiteSiteAliasType extends AbstractType
{
    protected $siteRepository;
    protected $nodeRepository;
    protected $currentSiteManager;

    /**
     * @param SiteRepositoryInterface    $siteRepository
     * @param NodeRepositoryInterface    $nodeRepository
     * @param ContextBackOfficeInterface $currentSiteManager
     */
    public function __construct(
        SiteRepositoryInterface $siteRepository,
        NodeRepositoryInterface $nodeRepository,
        ContextBackOfficeInterface $currentSiteManager
    ) {
        $this->siteRepository = $siteRepository;
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        if (!array_key_exists('siteId', $data)) {
            $data['siteId'] = $this->currentSiteManager->getSiteId();
        }

        $builder->add('siteId', 'oo_site_choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.site',
            'data' => $data['siteId'],
            'attr' => array(
                'class' => 'patch-submit-change',
                'data-key' => 'site'
            ),
            'required' => true,
        ));
        $builder->addEventSubscriber(
            new SiteSubscriber(
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
        $resolver->setDefaults(
            array(
                'attr' => array('class' => 'form-to-patch-and-send'),
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
