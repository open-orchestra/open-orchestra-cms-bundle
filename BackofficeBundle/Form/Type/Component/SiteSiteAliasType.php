<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\BackofficeBundle\EventSubscriber\SiteSubscriber;

/**
 * Class SiteSiteAliasType
 */
class SiteSiteAliasType extends AbstractType
{
    protected $siteSubscriber;
    protected $siteRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     * @param SiteSubscriber $siteSubscriber
     */
    public function __construct(SiteRepositoryInterface $siteRepository, SiteSubscriber $siteSubscriber)
    {
        $this->siteRepository = $siteRepository;
        $this->siteSubscriber = $siteSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('siteId', 'choice', array(
            'label' => false,
            'choices' => $this->getChoices(),
            'required' => false,
        ));

        $builder->addEventSubscriber($this->siteSubscriber);
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $sites = $this->siteRepository->findByDeleted(false);

        $choices = array();

        /** @var SiteInterface $site */
        foreach ($sites as $site) {
            $choices[$site->getSiteId()] = $site->getName();
        }

        return $choices;
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
