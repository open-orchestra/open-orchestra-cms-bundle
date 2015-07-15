<?php

namespace OpenOrchestra\MediaAdminBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use OpenOrchestra\MediaAdminBundle\Form\DataTransformer\EmbedSiteToSiteIdTransformer;

/**
 * Class OrchestraSiteForFolderChoiceType
 */
class OrchestraSiteForFolderChoiceType extends AbstractType
{
    protected $siteRepository;
    protected $tokenStorage;
    protected $embedSiteToSiteTransformer;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(
        SiteRepositoryInterface $siteRepository,
        TokenStorageInterface $tokenStorage,
        EmbedSiteToSiteIdTransformer $embedSiteToSiteIdTransformer
    )
    {
        $this->siteRepository = $siteRepository;
        $this->tokenStorage = $tokenStorage;
        $this->embedSiteToSiteIdTransformer = $embedSiteToSiteIdTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['embed']) {
            $builder->addModelTransformer($this->embedSiteToSiteIdTransformer);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'embed' => false,
                'choices' => $this->getChoices()
            )
        );
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $sites = $this->siteRepository->findByDeleted(false);

        $choices = array();

        $siteIds = array();
        /** @var SiteInterface $site */
        foreach ($sites as $site) {
            $siteIds[] = $site->getSiteId();
        }

        $userGroups = $this->tokenStorage->getToken()->getUser()->getGroups();
        /** @var GroupInterface $group */
        foreach ($userGroups as $group) {
            if (in_array($group->getSite()->getSiteId(), $siteIds) && ($group->hasRole('ROLE_ACCESS_TREE_FOLDER'))) {
                $choices[$group->getSite()->getSiteId()] = $group->getSite()->getName();
            }
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
        return 'orchestra_site_for_folder_choice';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
}
