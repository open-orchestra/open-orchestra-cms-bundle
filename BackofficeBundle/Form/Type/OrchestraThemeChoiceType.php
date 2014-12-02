<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\ModelBundle\Repository\ThemeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraThemeChoiceType
 */
class OrchestraThemeChoiceType extends AbstractType
{
    protected $themeRepository;

    /**
     * @param ThemeRepository $themeRepository
     */
    public function __construct(ThemeRepository $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getChoices()
        ));
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $choices = array();

        foreach ($this->themeRepository->findAll() as $theme) {
            $choices[$theme->getName()] = $theme->getName();
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
        return 'orchestra_theme_choice';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }
}
