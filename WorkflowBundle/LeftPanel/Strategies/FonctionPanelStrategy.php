<?php

namespace OpenOrchestra\WorkflowBundle\LeftPanel\Strategies;

use OpenOrchestra\Backoffice\LeftPanel\Strategies\AbstractLeftPanelStrategy;
use OpenOrchestra\Workflow\Repository\FonctionRepositoryInterface;

/**
 * Class TreeFonctionPanel
 */
class TreeFonctionPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_TREE_FONCTION = 'ROLE_ACCESS_TREE_FONCTION';

    /**
     * @var FonctionRepositoryInterface
     */
    protected $fonctionRepository;

    /**
     * @param FonctionRepositoryInterface $fonctionRepository
     */
    public function __construct(FonctionRepositoryInterface $fonctionRepository)
    {
        $this->fonctionRepository = $fonctionRepository;
    }

    /**
     * @return string
     */
    public function show()
    {
        $rootFonctions = $this->fonctionRepository->findAllRootFonctionBySiteId();

        return $this->render( 'OpenOrchestraWorkflowBundle:Tree:showFonctionTree.html.twig', array(
            'fonctions' => $rootFonctions,
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return self::EDITORIAL;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fonctions';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_TREE_FONCTION;
    }
}
