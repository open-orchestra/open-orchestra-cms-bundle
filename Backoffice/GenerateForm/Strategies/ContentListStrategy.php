<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ContentListStrategy
 */
class ContentListStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CONTENT_LIST === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $empty = array(
            'keywords' => null,
            'url' => '',
            'characterNumber' => 50,
            'contentType' => ''
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('url', 'orchestra_node_choice', array(
            'mapped' => false,
            'data' => $attributes['url'],
            'label' => 'php_orchestra_backoffice.form.content_list.node',
        ));
        $form->add('characterNumber', 'text', array(
            'mapped' => false,
            'data' => $attributes['characterNumber'],
            'label' => 'php_orchestra_backoffice.form.content_list.nb_characters',
            'required' => false,
        ));
        $form->add('contentType', 'orchestra_content_type_choice', array(
            'mapped' => false,
            'data' => $attributes['contentType'],
            'label' => 'php_orchestra_backoffice.form.content_list.content_type',
            'required' => false
        ));
        $form->add('keywords', 'orchestra_keywords', array(
            'mapped' => false,
            'data' => $attributes['keywords'],
            'label' => 'php_orchestra_backoffice.form.content_list.content_keyword',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_list';
    }
}
