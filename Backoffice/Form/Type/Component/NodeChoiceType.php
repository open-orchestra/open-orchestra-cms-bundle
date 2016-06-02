<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\DisplayBundle\Manager\TreeManager;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NodeChoiceType
 */
class NodeChoiceType extends AbstractType
{
    protected $nodeRepository;
    protected $treeManager;
    protected $currentSiteManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param TreeManager $treeManager
     * @param CurrentSiteIdInterface $currentSiteManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, TreeManager $treeManager, CurrentSiteIdInterface $currentSiteManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->treeManager = $treeManager;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->getChoices(),
            )
        );

        /*
         * Normalize 'attr' option to force presence of 'orchestra-node-choice' html class.
         * This class is mandatory to trigger execution of following coffee function on field :
         * OpenOrchestra.FormBehavior.NodeChoice.activateBehaviorOnElements().
         */
        $resolver->setNormalizer('attr', function (Options $options, $value) {
            if (!is_array($value)) {
                $value = array();
            }

            $classList = array();
            if (array_key_exists('class', $value) && !empty($value['class'])) {
                $classList = explode(' ', $value['class']);
            }
            if (!in_array('orchestra-node-choice', $classList)) {
                $classList[] = 'orchestra-node-choice';
            }
            $value['class'] = implode(' ', $classList);

            return $value;
        });
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $nodes = $this->nodeRepository->findLastVersionByType($siteId);
        $orderedNodes = $this->treeManager->generateTree($nodes);

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
            $pre = '';
            if ($depth > 0) {
                $pre = str_repeat('&#x2502;', $depth - 1).'&#x251C;';
            }
            $choices[$node['node']->getNodeId()] = $pre.$node['node']->getName();

            if (array_key_exists('child', $node)) {
                $choices = array_merge($choices, $this->getHierarchicalChoices($node['child'], $depth + 1));
            }
        }
        if (isset($node)) {
            $pre = '';
            if ($depth > 0) {
                $pre = str_repeat('&#x2502;', $depth - 1).'&#x2514;';
            }
            $choices[$node['node']->getNodeId()] = $pre.$node['node']->getName();
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
