<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\ApiBundle\Transformer\TransformerManager;
use PHPOrchestra\ModelBundle\Repository\ContentTypeRepository;
use PHPOrchestra\ModelBundle\Repository\ContentRepository;
use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ConfigurableContentStrategy
 */
class ConfigurableContentStrategy extends AbstractBlockStrategy
{
    protected $contentTypeRepository = null;
    protected $contentRepository = null;
    protected $router = null;
    
    /**
     * Constructor
     * 
     * @param $contentTypeRepository
     * @param $contentRepository
     * @param $router
     */
    public function __construct(
        ContentTypeRepository $contentTypeRepository,
        ContentRepository $contentRepository,
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
        
        $form->add(
            'contentTypeId',
            'choice',
            array(
                'mapped' => false,
                'required' => false,
                'choices' => $choices,
                'data' => $contentTypeId,
                'attr' => array(
                    'class' => 'contentTypeSelector',
                    'data-url' => $this->router->generate('php_orchestra_api_content_list')
                )
            )
        );
        
        $this->updateContentChoice($form, $block);
        
    }

    /**
     * (non-PHPdoc)
     * @see itkg/phporchestra-cms-bundle/PHPOrchestra/Backoffice/GenerateForm/Strategies/PHPOrchestra\Backoffice\GenerateForm\Strategies.AbstractBlockStrategy::alterFormAfterSubmit()
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
            'label' => 'Content'
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
        return 'ConfigurableContent';
    }

}
