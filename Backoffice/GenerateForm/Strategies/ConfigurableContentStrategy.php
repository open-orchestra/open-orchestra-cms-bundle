<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use PHPOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
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
        return DisplayBlockInterface::CONFIGURABLE_CONTENT === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $contentTypeId = '';
        if (array_key_exists('contentTypeId', $attributes)) {
            $contentTypeId = $attributes['contentTypeId'];
        }

        $choices = array();
        $contentTypes = $this->contentTypeRepository->findAll();
        if (!empty($contentTypes)) {
            foreach ($contentTypes as $contentType) {
                $choices[$contentType->getContentTypeId()] = $contentType->getName();
            }

        }

        $form->add('contentTypeId', 'choice', array(
            'mapped' => false,
            'required' => false,
            'choices' => $choices,
            'data' => $contentTypeId,
            'attr' => array(
                'class' => 'contentTypeSelector',
                'data-url' => $this->router->generate('php_orchestra_api_content_list')
            ),
            'label' => 'php_orchestra_backoffice.block.configurable_content.contentTypeId'
        ));

        $this->updateContentChoice($form, $block);

    }

    /**
     * Refresh the content choice after submission and before validation
     *
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function alterFormAfterSubmit(FormInterface $form, BlockInterface $block)
    {
        $this->updateContentChoice($form, $block);
    }

    /**
     * Update form by populating contents choice according to content type selected
     *
     * @param FormInterface $form
     * @param BlockInterface $block
     */
    protected function updateContentChoice(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $contentTypeId = '';
        if (array_key_exists('contentTypeId', $attributes)) {
            $contentTypeId = $attributes['contentTypeId'];
        }
        $contentId = '';
        if (array_key_exists('contentId', $attributes)) {
            $contentId = $attributes['contentId'];
        }

        $contents = array();
        if ($contentTypeId != '') {
            $criteria = array(
                'deleted' => false,
                'contentType' => $contentTypeId
            );

            $contentCollection = $this->contentRepository->findBy($criteria);

            foreach($contentCollection as $content) {
                $contents[$content->getContentId()] = $content->getName();
            }
        }

        $options = array(
            'mapped' => false,
            'choices' => $contents,
            'label' => 'php_orchestra_backoffice.block.configurable_content.contentId'
        );
        if ($contentId != '') {
            $options['data'] = $contentId;
        }

        $form->add('contentId', 'choice', $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'configurable_content';
    }

}
