<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\Validator\Constraints\UniqueBlockCode;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
    protected $blockClass;
    protected $templateManager;
    protected $contextManager;
    protected $generateFormManager;
    protected $siteRepository;
    protected $blockToArrayTransformer;
    protected $blockFormTypeSubscriber;
    protected $displayBlockManager;

    /**
     * @param TemplateManager          $templateManager
     * @param CurrentSiteIdInterface   $contextManager
     * @param GenerateFormManager      $generateFormManager
     * @param SiteRepositoryInterface  $siteRepository
     * @param BlockToArrayTransformer  $blockToArrayTransformer
     * @param EventSubscriberInterface $blockFormTypeSubscriber
     * @param DisplayBlockManager      $displayBlockManager
     */
    public function __construct(
        TemplateManager $templateManager,
        CurrentSiteIdInterface $contextManager,
        SiteRepositoryInterface $siteRepository,
        GenerateFormManager $generateFormManager,
        BlockToArrayTransformer $blockToArrayTransformer,
        EventSubscriberInterface $blockFormTypeSubscriber,
        DisplayBlockManager $displayBlockManager
    ) {
        $this->templateManager = $templateManager;
        $this->contextManager = $contextManager;
        $this->siteRepository = $siteRepository;
        $this->generateFormManager = $generateFormManager;
        $this->blockToArrayTransformer = $blockToArrayTransformer;
        $this->blockFormTypeSubscriber = $blockFormTypeSubscriber;
        $this->displayBlockManager = $displayBlockManager;
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
            'choices' => $this->getStyleChoices($options),
            'group_id' => 'property',
            'sub_group_id' => 'style',
        ));
        $this->addFieldMaxAge($builder, $options);
        if (
            isset($options['data']) &&
            $options['data'] instanceof BlockInterface &&
            $options['data']->isTransverse()
        ) {
            $builder->add('code', 'text', array(
                'label' => 'open_orchestra_backoffice.form.block.code',
                'required' => false,
                'group_id' => 'technical',
                'sub_group_id' => 'code',
                'constraints' => new UniqueBlockCode(array(
                    'block' => $options['data'],
                ))
            ));
        }

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
                'data_class' => null,
                'delete_button' => false,
                'new_button' => false,
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
                    )
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
                    'code' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.block.sub_group.code',
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
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['delete_button'] = $options['delete_button'];
        $view->vars['new_button'] = $options['new_button'];
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
     * @param array $options
     *
     * @return array
     */
    protected function getStyleChoices(array $options)
    {
        $choices = array();
        if (
            isset($options['data']) &&
            $options['data'] instanceof BlockInterface
        ) {
            $siteId = $this->contextManager->getCurrentSiteId();
            $site = $this->siteRepository->findOneBySiteId($siteId);
            $templateSetId = $site->getTemplateSet();
            $templateSetParameters = $this->templateManager->getTemplateSetParameters();
            $blockComponent = $options['data']->getComponent();

            foreach ($templateSetParameters[$templateSetId]['styles'] as $key => $configuration) {
                if (
                    (isset($configuration['allowed_blocks']) && isset($configuration['label'])) &&
                    (empty($configuration['allowed_blocks']) || in_array($blockComponent, $configuration['allowed_blocks']))
                ) {
                    $choices[$key] = $configuration['label'];
                }
            }
        }

        return $choices;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function addFieldMaxAge(FormBuilderInterface $builder, array $options)
    {
        if (
            !isset($options['data']) || (
            $options['data'] instanceof BlockInterface &&
            true === $this->displayBlockManager->isPublic($options['data'])
        )) {
            $builder->add('maxAge', 'integer', array(
                'label' => 'open_orchestra_backoffice.form.block.max_age',
                'required' => false,
                'group_id' => 'technical',
                'sub_group_id' => 'cache',
            ));
        }
    }
}
