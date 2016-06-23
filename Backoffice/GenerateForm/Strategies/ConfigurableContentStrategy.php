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
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param ContentRepositoryInterface     $contentRepository
     * @param UrlGeneratorInterface          $router
     * @param ContextManager                 $context
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        ContentRepositoryInterface $contentRepository,
        $router,
        ContextManager $context
    ) {
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
            'refresh' => true,
            'required' => true,
            'authorize_new' => false,
        ));

        $builder->add('contentTemplateEnabled', 'checkbox', array(
            'label' => 'open_orchestra_backoffice.block.configurable_content.content_template_enabled.title',
            'attr' => array('help_text' => 'open_orchestra_backoffice.block.configurable_content.content_template_enabled.helper'),
            'required' => false,
        ));
        $builder->add('contentTemplate', 'oo_tinymce', array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.block.configurable_content.content_template',
            'constraints' => new ContentTemplate(),
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
