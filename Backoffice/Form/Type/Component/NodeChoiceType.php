<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Form\AbstractType;
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
     * @param CurrentSiteIdInterface $currentSiteManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, CurrentSiteIdInterface $currentSiteManager)
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
                'siteId' => $this->currentSiteManager->getCurrentSiteId(),
                'language' => $this->currentSiteManager->getCurrentSiteDefaultLanguage(),
                'attr' => array(
                    'class' => 'orchestra-node-choice'
                )
            )
        );
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
            $pre = '';
            if ($depth > 0) {
                $pre = str_repeat('&#x2502;', $depth - 1).'&#x251C;';
            }
            $choices[$node['node']['nodeId']] = $pre.$node['node']['name'];

            if (array_key_exists('child', $node)) {
                $choices = array_merge($choices, $this->getHierarchicalChoices($node['child'], $depth + 1));
            }
        }
        if (isset($node)) {
            $pre = '';
            if ($depth > 0) {
                $pre = str_repeat('&#x2502;', $depth - 1).'&#x2514;';
            }
            $choices[$node['node']['nodeId']] = $pre.$node['node']['name'];
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
