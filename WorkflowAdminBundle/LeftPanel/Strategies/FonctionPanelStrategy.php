<?php

namespace OpenOrchestra\WorkflowAdminBundle\LeftPanel\Strategies;

use OpenOrchestra\Backoffice\LeftPanel\Strategies\AbstractLeftPanelStrategy;
use OpenOrchestra\Fonction\Repository\FonctionRepositoryInterface;

/**
 * Class FonctionPanelStrategy
 */
class FonctionPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_FONCTION = 'ROLE_ACCESS_FONCTION';

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
        return $this->render('OpenOrchestraWorkflowAdminBundle:AdministrationPanel:fonctions.html.twig');
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return self::ADMINISTRATION;
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
        return self::ROLE_ACCESS_FONCTION;
    }
}
