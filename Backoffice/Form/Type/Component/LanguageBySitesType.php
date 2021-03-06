<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\UserBundle\Document\User;

/**
 * Class LanguageBySitesType
 */
class LanguageBySitesType extends AbstractType
{
    protected $siteRepository;
    protected $frontLanguages;

    /**
     * @param SiteRepositoryInterface $siteRepository
     * @param array                   $frontLanguages
     */
    public function __construct(
        SiteRepositoryInterface $siteRepository,
        array $frontLanguages
    ){
        $this->siteRepository = $siteRepository;
        $this->frontLanguages = $frontLanguages;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['sites_id'] as $siteId) {
            $site = $this->siteRepository->findOneBySiteId(preg_replace('/^('.preg_quote(User::SITE_ID_PREFIX).')(.*)$/', '$2', $siteId));
            if (null !== $site) {
                $aliasLanguages = $site->getLanguages();
                $builder
                    ->add($siteId, 'choice', array(
                        'choices' => $this->frontLanguages,
                        'choice_attr' => function ($key, $val, $index) use ($aliasLanguages) {
                            return in_array($key, $aliasLanguages) ? array() : array('disabled' => 'disabled');
                        },
                        'label' => $site->getName(),
                        'expanded' => true,
                ));
            }
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'sites_id' => array(),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_language_by_sites';
    }
}
