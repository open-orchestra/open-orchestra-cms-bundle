<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ContentListByTypeStrategy
 */
class ContentListByTypeStrategy extends AbstractContentListStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CONTENT_LIST_BY_TYPE=== $block->getComponent();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_list_by_type';
    }

    /**
     * @return array
     */
    protected function getEmptyArray()
    {
        return array('contentType' => '');
    }

    /**
     * @param FormInterface $form
     * @param array $attributes
     */
    protected function startBuildForm(FormInterface $form, $attributes)
    {
        $form->add('contentType', 'orchestra_content_type_choice', array(
            'mapped' => false,
            'data' => $attributes['contentType'],
            'label' => 'php_orchestra_backoffice.form.content_list.content_type',
        ));
    }
}
