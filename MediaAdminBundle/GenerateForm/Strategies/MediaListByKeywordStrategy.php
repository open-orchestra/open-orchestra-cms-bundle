<?php

namespace OpenOrchestra\MediaAdminBundle\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Media\DisplayBlock\Strategies\MediaListByKeywordStrategy as BaseMediaListByKeywordStrategy;

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
        return BaseMediaListByKeywordStrategy::MEDIA_LIST_BY_KEYWORD === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('keywords', 'orchestra_keywords', array(
            'embedded' => false,
            'label' => 'open_orchestra_media_admin.form.media.list.keyword',
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
