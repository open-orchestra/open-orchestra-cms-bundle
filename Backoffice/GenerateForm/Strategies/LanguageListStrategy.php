<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class LanguageChoiceStrategy
 */
class LanguageListStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::LANGUAGE_LIST === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $form->add('languages', 'orchestra_language_list', array(
            'mapped' => false,
            'data' => array_key_exists('languages', $attributes)? json_encode($attributes['languages']):json_encode(
                array(
                    'fr',
                    'en',
                )
            ),
        ));
        $form->add('default', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('default', $attributes)? json_encode($attributes['default']):'fr'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'language_list';
    }
}
