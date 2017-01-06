<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\Backoffice\Form\DataTransformer\BlockToArrayTransformer;
use OpenOrchestra\Backoffice\Manager\TemplateManager;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * Class BlockType
 */
class BlockType extends AbstractType
{
    protected $templateManager;
    protected $contextManager;
    protected $generateFormManager;
    protected $siteRepository;
    protected $blockToArrayTransformer;
    protected $blockFormTypeSubscriber;

    /**
     * @param TemplateManager          $templateManager
     * @param CurrentSiteIdInterface   $contextManager
     * @param GenerateFormManager      $generateFormManager
     * @param SiteRepositoryInterface  $siteRepository
     * @param BlockToArrayTransformer  $blockToArrayTransformer
     * @param EventSubscriberInterface $blockFormTypeSubscriber
     */
    public function __construct(
        TemplateManager $templateManager,
        CurrentSiteIdInterface $contextManager,
        SiteRepositoryInterface $siteRepository,
        GenerateFormManager $generateFormManager,
        BlockToArrayTransformer $blockToArrayTransformer,
        EventSubscriberInterface $blockFormTypeSubscriber
    ) {
        $this->templateManager = $templateManager;
        $this->contextManager = $contextManager;
        $this->siteRepository = $siteRepository;
        $this->generateFormManager = $generateFormManager;
        $this->blockToArrayTransformer = $blockToArrayTransformer;
        $this->blockFormTypeSubscriber = $blockFormTypeSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'text', array(
            'label' => 'open_orchestra_backoffice.form.block.label',
            'constraints' => new NotBlank(),
            'group_id' => 'property',
            'sub_group_id' => 'property',
        ));
        $builder->add('style', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.block.style',
            'required' => false,
            'choices' => $this->getStyleChoices(),
            'group_id' => 'property',
            'sub_group_id' => 'style',
        ));
        $builder->add('maxAge', 'integer', array(
            'label' => 'open_orchestra_backoffice.form.block.max_age',
            'required' => false,
            'group_id' => 'technical',
            'sub_group_id' => 'cache',
        ));

        $builder->setAttribute('template', $this->generateFormManager->getTemplate($options['data']));

        $builder->addViewTransformer($this->blockToArrayTransformer);
        $builder->addEventSubscriber($this->blockFormTypeSubscriber);

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'blockPosition' => 0,
                'data_class' => null,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.block.group.property',
                    ),
                    'data' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.block.group.data',
                    ),
                    'technical' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.block.group.technical',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.block.sub_group.property',
                    ),
                    'style' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.block.sub_group.style',
                    ),
                    'content' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.block.sub_group.content',
                    ),
                    'cache' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.block.sub_group.cache',
                    ),
                    'html' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.block.sub_group.html',
                    ),
                ),
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_block';
    }

    /**
     * @return array
     */
    protected function getStyleChoices()
    {
        $siteId = $this->contextManager->getCurrentSiteId();
        $site = $this->siteRepository->findOneBySiteId($siteId);
        $templateSetId = $site->getTemplateSet();
        $templateSetParameters = $this->templateManager->getTemplateSetParameters();
        $choices = array();
        foreach ($templateSetParameters[$templateSetId]['styles'] as $key => $label) {
            $choices[$key] = $label;
        }

        return $choices;
    }
}
