<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ContentListByKeywordStrategy
 */
class ContentListByKeywordStrategy extends AbstractContentListStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CONTENT_LIST_BY_KEYWORD === $block->getComponent();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_list_by_keyword';
    }

    /**
     * @return array
     */
    protected function getEmptyArray()
    {
        return array('contentKeyword' => '');
    }

    /**
     * @param FormInterface $form
     * @param array         $attributes
     */
    protected function startBuildForm(FormInterface $form, $attributes)
    {
        $form->add('contentKeyword', 'orchestra_keyword_choice', array(
            'mapped' => false,
            'data' => $attributes['contentType'],
            'label' => 'php_orchestra_backoffice.form.content_list.content_keyword',
        ));
    }
}
