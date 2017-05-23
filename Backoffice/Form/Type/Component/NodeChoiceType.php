<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Class NodeChoiceType
 */
class NodeChoiceType extends AbstractType
{
    protected $nodeRepository;
    protected $currentSiteManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param ContextBackOfficeInterface $currentSiteManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, ContextBackOfficeInterface $currentSiteManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => function(Options $options) {
                    return $this->getChoices($options['siteId'], $options['language']);
                },
                'siteId' => $this->currentSiteManager->getSiteId(),
                'language' => $this->currentSiteManager->getSiteDefaultLanguage(),
                'attr' => array(
                    'class' => 'orchestra-tree-choice'
                )
            )
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form ,array $options)
    {
        $orderedNodes = $this->nodeRepository->findTreeNode($options['siteId'], $options['language'], NodeInterface::ROOT_NODE_ID);
        $orderedNodesAttributs = $this->getHierarchicalChoicesAttributes($orderedNodes);
        $result = array();
        foreach ($view->vars['choices'] as $node) {
            $result[] = new ChoiceView(
                $node->data,
                $node->value,
                $node->label,
                $orderedNodesAttributs[$node->data]
            );
        }
        $view->vars['choices'] = $result;
    }

    /**
     * @param string $siteId
     * @param string $language
     *
     * @return array
     */
    protected function getChoices($siteId, $language)
    {
        $orderedNodes = $this->nodeRepository->findTreeNode($siteId, $language, NodeInterface::ROOT_NODE_ID);

        return $this->getHierarchicalChoices($orderedNodes);
    }

    /**
     * @param array $nodes
     * @param int   $depth
     *
     * @return array
     */
    protected function getHierarchicalChoices($nodes, $depth = 0)
    {
        $choices = array();
        foreach ($nodes as $node) {
            $choices[$node['node']['nodeId']] = $node['node']['name'];
            if (array_key_exists('child', $node)) {
                $choices = array_merge($choices, $this->getHierarchicalChoices($node['child'], $depth + 1));
            }
        }

        return $choices;

    }

    /**
     * @param array $nodes
     * @param int   $depth
     *
     * @return array
     */
    protected function getHierarchicalChoicesAttributes($nodes, $depth = 0)
    {
        $choices = array();
        $lastNode = end($nodes);
        foreach ($nodes as $node) {
            $choices[$node['node']['nodeId']] = array(
                'data-depth' => $depth,
                'data-last' => $node === $lastNode,
            );
            if (array_key_exists('child', $node)) {
                $choices = array_merge($choices, $this->getHierarchicalChoicesAttributes($node['child'], $depth + 1));
            }
        }

        return $choices;

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_node_choice';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
}
