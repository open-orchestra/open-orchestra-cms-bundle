<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AudienceAnalysisStrategy as BaseAudienceAnalysisStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
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
            'google_analytics' => $translator->trans('open_orchestra_backoffice.block.audience_analysis.google_analytics'),
            'xiti_free' => $translator->trans('open_orchestra_backoffice.block.audience_analysis.xiti_free')
        );
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseAudienceAnalysisStrategy::AUDIENCE_ANALYSIS === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag_type', 'choice', array(
                'choices' => $this->choices,
                'label' => 'open_orchestra_backoffice.block.audience_analysis.tag_type'
            ))
            ->add('site_id', 'text', array(
                'label' => 'open_orchestra_backoffice.block.audience_analysis.site_id'
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
