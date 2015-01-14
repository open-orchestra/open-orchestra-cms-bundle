<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class MediaListByKeywordStrategy
 */
class MediaListByKeywordStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::MEDIA_LIST_BY_KEYWORD === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $attributes = array_merge(array(
            'keywords' => null,
        ), $attributes);

        $form->add('keywords', 'orchestra_keywords', array(
            'mapped' => false,
            'data' => $attributes['keywords'],
            'label' => 'php_orchestra_backoffice.form.media.list.keyword',
            'required' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'media_list_by_keyword';
    }
}
