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

        $form->add('contentTypeName', 'orchestra_content_type_choice', array(
            'mapped' => false,
            'data' => array_key_exists('contentType', $attributes)? $attributes['contentType']:'',
            'label' => 'php_orchestra_backoffice.form.content_list.node',
        ));
        $form->add('class', 'textarea', array(
            'mapped' => false,
            'data' => array_key_exists('class', $attributes)? json_encode($attributes['class']):json_encode(
                array(
                    'div' => 'divclass',
                    'title' => 'titleclass',
                    'ul' => 'ulclass'
                )
            ),
        ));
        $form->add('id', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('id', $attributes)? $attributes['id']:'',
        ));
        $form->add('url', 'orchestra_node_choice', array(
            'mapped' => false,
            'data' => array_key_exists('url', $attributes)? $attributes['url']:'',
            'label' => 'php_orchestra_backoffice.form.content_list.node',
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
