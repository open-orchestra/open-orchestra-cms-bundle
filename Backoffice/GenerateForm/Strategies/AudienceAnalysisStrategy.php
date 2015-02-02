<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AudienceAnalysisStrategy
 */
class AudienceAnalysisStrategy extends AbstractBlockStrategy
{
    protected $choices;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->choices = array(
            'google_analytics' => $translator->trans('php_orchestra_backoffice.block.audience_analysis.google_analytics.label'),
            'xiti_free' => $translator->trans('php_orchestra_backoffice.block.audience_analysis.xiti_free.label')
        );
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::AUDIENCE_ANALYSIS === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $form
            ->add('tag_type', 'choice', array(
                'mapped' => false,
                'choices' => $this->choices,
//                'expanded' => true,
                'data' => array_key_exists('tag_type', $attributes) ? $attributes['tag_type'] : '',
                'label' => 'php_orchestra_backoffice.block.audience_analysis.tag_type'
            ))
            ->add('xiti_site', 'text', array(
                'mapped' => false,
                'data' => array_key_exists('xiti_site', $attributes) ? $attributes['xiti_site'] : '',
                'label' => 'php_orchestra_backoffice.block.audience_analysis.xiti_free.site_id'
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'audience_analysis';
    }
}
