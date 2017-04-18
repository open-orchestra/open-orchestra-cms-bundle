<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Backoffice\EventSubscriber\SiteSubscriber;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Class SiteSiteAliasType
 */
class SiteSiteAliasType extends AbstractType
{
    protected $siteRepository;
    protected $currentSiteManager;

    /**
     * @param SiteRepositoryInterface $siteRepository
     * @param CurrentSiteIdInterface  $currentSiteManager
     */
    public function __construct(SiteRepositoryInterface $siteRepository, CurrentSiteIdInterface $currentSiteManager)
    {
        $this->siteRepository = $siteRepository;
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
            $data['siteId'] = $this->currentSiteManager->getCurrentSiteId();
        }

        $builder->add('siteId', 'oo_site_choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.site',
            'data' => $data['siteId'],
            'attr' => array(
                'class' => 'to-tinyMce patch-submit-change',
                'data-key' => 'site'
            ),
            'required' => true,
        ));
        $builder->addEventSubscriber(
            new SiteSubscriber(
                $this->siteRepository,
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
                'attr' => array('class' => 'form-to-patch'),
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
