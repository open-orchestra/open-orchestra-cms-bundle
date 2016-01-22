<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\ThemeRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ThemeChoiceType
 */
class ThemeChoiceType extends AbstractType
{
    protected $themeRepository;
    protected $defaultChoice;
    /**
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(ThemeRepositoryInterface $themeRepository)
    {
        $this->themeRepository = $themeRepository;
        $this->defaultChoice = array(NodeInterface::THEME_DEFAULT => "open_orchestra_backoffice.form.theme.site_theme");
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
        $choices = $this->defaultChoice;

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
        return 'oo_theme_choice';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
}
