<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use PHPOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ConfigurableContentStrategy
 */
class ConfigurableContentStrategy extends AbstractBlockStrategy
{
    protected $contentTypeRepository;
    protected $contentRepository;
    protected $router;
    protected $block;

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
        $this->block = $block;
        return DisplayBlockInterface::CONFIGURABLE_CONTENT === $block->getComponent();
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
                'data-url' => $this->router->generate('php_orchestra_api_content_list')
            ),
            'label' => 'php_orchestra_backoffice.block.configurable_content.contentTypeId'
        ));


        $contentCollection = $this->contentRepository->findBy(array('deleted' => false));

        foreach($contentCollection as $content) {
            $contents[$content->getContentId()] = $content->getName();
        }

        $options = array(
            'choices' => $contents,
            'label' => 'php_orchestra_backoffice.block.configurable_content.contentId'
        );

        $builder->add('contentId', 'choice', $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'configurable_content';
    }

}
