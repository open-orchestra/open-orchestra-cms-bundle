<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\ModelInterface\Repository\TemplateFlexRepositoryInterface;

/**
 * Class TreeTemplateFlexPanelStrategy
 */
class TreeTemplateFlexPanelStrategy extends AbstractNavigationPanelStrategy
{
    const ROLE_ACCESS_TREE_TEMPLATE = 'ROLE_ACCESS_TREE_TEMPLATE';
    const ROLE_ACCESS_CREATE_TEMPLATE = 'ROLE_ACCESS_CREATE_TEMPLATE';
    const ROLE_ACCESS_UPDATE_TEMPLATE = 'ROLE_ACCESS_UPDATE_TEMPLATE';
    const ROLE_ACCESS_DELETE_TEMPLATE = 'ROLE_ACCESS_DELETE_TEMPLATE';

    /**
     * @var TemplateFlexRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @param TemplateFlexRepositoryInterface $templateRepository
     * @param string                          $parent
     * @param int                             $weight
     */
    public function __construct(TemplateFlexRepositoryInterface $templateRepository, $parent, $weight)
    {
        parent::__construct('templates_flex', self::ROLE_ACCESS_TREE_TEMPLATE, $weight, $parent);
        $this->templateRepository = $templateRepository;
    }

    /**
     * @return string
     */
    public function show()
    {
        $templates = $this->templateRepository->findByDeleted(false);

        return $this->render(
            'OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/Editorial/templates-flex.html.twig',
            array(
                'templates' => $templates
            )
        );
    }
}
