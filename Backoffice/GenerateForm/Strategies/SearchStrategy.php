<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SearchStrategy
 */
class SearchStrategy extends AbstractBlockStrategy
{
    protected $nodeRepository;
    protected $contextManager;

    /**
     * @param NodeRepositoryInterface     $nodeRepository,
     * @param ContextBackOfficeInterface  $contextManager,
     * @param array                       $basicBlockConfiguration
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        ContextBackOfficeInterface $contextManager,
        array $basicBlockConfiguration
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->contextManager = $contextManager;
        parent::__construct($basicBlockConfiguration);
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return $this->getName() === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'text', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('nodeId', 'choice', array(
            'choices' => $this->getSpecialPageList(),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('limit', 'integer', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array('limit' => 7);
    }

    /**
     * get special pages list
     *
     * @return array
     */
    protected function getSpecialPageList() {
        $siteId = $this->contextManager->getSiteId();
        $language = $this->contextManager->getSiteDefaultLanguage();
        $specialPages = $this->nodeRepository->findAllSpecialPage($language, $siteId);

        $specialPageChoice = array();
        foreach ($specialPages as $node) {
            $specialPageChoice[$node->getId()] = $node->getSpecialPageName();
        }

        return $specialPageChoice;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'search';
    }

}
