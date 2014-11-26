<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
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
            'contentType' => '',
            'class' => '',
            'id' => '',
            'url' => '',
            'characterNumber' => 50,
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('contentTypeName', 'orchestra_content_type_choice', array(
            'mapped' => false,
            'data' => $attributes['contentType'],
            'label' => 'php_orchestra_backoffice.form.content_list.content_type',
        ));
        $form->add('class', 'textarea', array(
            'mapped' => false,
            'data' => $attributes['class'],
            'required' => false,
        ));
        $form->add('id', 'text', array(
            'mapped' => false,
            'data' => $attributes['id'],
            'required' => false,
        ));
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
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_list';
    }
}
