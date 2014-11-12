<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\Backoffice\Context\ContextManager;
use PHPOrchestra\ModelBundle\Repository\SiteRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraLanguageList
 */
class OrchestraLanguageListType extends AbstractType
{
    protected $contextManager;
    protected $siteRepository;

    /**
     * @param ContextManager $contextManager
     * @param SiteRepository $siteRepository
     */
    public function __construct(ContextManager $contextManager, SiteRepository $siteRepository)
    {
        $this->contextManager = $contextManager;
        $this->siteRepository = $siteRepository;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'textarea' => $this->getList()
            )
        );
    }

    protected function getList()
    {
        $site = $this->siteRepository->findBySiteId($this->contextManager->getCurrentSiteId());

        return $site[0]->getLanguages();
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'orchestra_language_list';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'textarea';
    }
}
