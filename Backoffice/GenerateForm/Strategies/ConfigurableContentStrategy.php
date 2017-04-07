<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\Backoffice\Validator\Constraints\ContentTemplate;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ConfigurableContentStrategy as BaseConfigurableContentStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ConfigurableContentStrategy
 */
class ConfigurableContentStrategy extends AbstractBlockStrategy
{
    protected $contentTypeRepository;
    protected $contentRepository;
    protected $router;
    protected $context;

    /**
     * @param array                          $basicBlockConfiguration
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param ContentRepositoryInterface     $contentRepository
     * @param UrlGeneratorInterface          $router
     * @param ContextManager                 $context
     */
    public function __construct(
        array $basicBlockConfiguration,
        ContentTypeRepositoryInterface $contentTypeRepository,
        ContentRepositoryInterface $contentRepository,
        $router,
        ContextManager $context
    ) {
        parent::__construct($basicBlockConfiguration);
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentRepository = $contentRepository;
        $this->router = $router;
        $this->context = $context;
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseConfigurableContentStrategy::NAME === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentSearch', 'oo_content_search', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.content',
            'search_engine' => true,
            'attr' => array('class' => 'form-to-patch'),
            'required' => true,
            'authorize_new' => false,
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));

        $builder->add('contentTemplateEnabled', 'checkbox', array(
            'label' => 'open_orchestra_backoffice.block.configurable_content.content_template_enabled.title',
            'attr' => array('help_text' => 'open_orchestra_backoffice.block.configurable_content.content_template_enabled.helper'),
            'required' => false,
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('contentTemplate', 'oo_tinymce', array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.block.configurable_content.content_template',
            'constraints' => new ContentTemplate(),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'configurable_content';
    }

}
