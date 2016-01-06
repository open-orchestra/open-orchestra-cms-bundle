<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;

/**
 * Class TreeTemplatePanel
 */
class GSTreeTemplatePanelStrategy extends AbstractNavigationPanelStrategy
{
    const ROLE_ACCESS_TREE_TEMPLATE = 'ROLE_ACCESS_TREE_TEMPLATE';

    /**
     * @var TemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @param TemplateRepositoryInterface $templateRepository
     * @param string                      $parent
     * @param int                         $weight
     */
    public function __construct(TemplateRepositoryInterface $templateRepository, $parent, $weight)
    {
        parent::__construct('GStemplates', self::ROLE_ACCESS_TREE_TEMPLATE, $weight, $parent, array(), null);
        $this->templateRepository = $templateRepository;
    }

    /**
     * @return string
     */
    public function show()
    {
        $templates = $this->templateRepository->findByDeleted(false);

        return $this->render(
            'OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/Editorial/gsTemplates.html.twig',
            array(
                'templates' => $templates
            )
        );
    }
}
