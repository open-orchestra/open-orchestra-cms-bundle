<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractContentListStrategy
 */
abstract class AbstractContentListStrategy extends AbstractBlockStrategy
{
    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $empty = array_merge(
            array(
                'keywords' => null,
                'class' => '',
                'id' => '',
                'url' => '',
                'characterNumber' => 50,
            ),
            $this->getEmptyArray()
        );

        $attributes = array_merge($empty, $attributes);

        $this->startBuildForm($form, $attributes);
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
     * @return array
     */
    abstract protected function getEmptyArray();

    /**
     * @param FormInterface $form
     * @param array         $attributes
     */
    abstract protected function startBuildForm(FormInterface $form, $attributes);
}
