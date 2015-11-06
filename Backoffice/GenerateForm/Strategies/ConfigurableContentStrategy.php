<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\ContentTemplate;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ConfigurableContentStrategy as BaseConfigurableContentStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ConfigurableContentStrategy
 */
class ConfigurableContentStrategy extends AbstractBlockStrategy
{
    protected $contentTypeRepository;
    protected $contentRepository;
    protected $router;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param ContentRepositoryInterface     $contentRepository
     * @param UrlGeneratorInterface          $router
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        ContentRepositoryInterface $contentRepository,
        $router
    ) {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentRepository = $contentRepository;
        $this->router = $router;
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseConfigurableContentStrategy::CONFIGURABLE_CONTENT === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array();
        $contentTypes = $this->contentTypeRepository->findAll();
        if (!empty($contentTypes)) {
            foreach ($contentTypes as $contentType) {
                $choices[$contentType->getContentTypeId()] = $contentType->getName();
            }

        }

        $builder->add('contentTypeId', 'choice', array(
            'required' => false,
            'choices' => $choices,
            'attr' => array(
                'class' => 'contentTypeSelector',
                'data-url' => $this->router->generate('open_orchestra_api_content_list')
            ),
            'label' => 'open_orchestra_backoffice.block.configurable_content.contentTypeId'
        ));


        $contentCollection = $this->contentRepository->findBy(array('deleted' => false));

        $contents = array();
        foreach($contentCollection as $content) {
            $contents[$content->getContentId()] = $content->getName();
        }

        $options = array(
            'constraints' => new NotBlank(),
            'choices' => $contents,
            'label' => 'open_orchestra_backoffice.block.configurable_content.contentId'
        );

        $builder->add('contentId', 'choice', $options);

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
