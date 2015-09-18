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
     * @param                             $weight
     */
    public function __construct(TemplateRepositoryInterface $templateRepository, $parent, $weight)
    {
        $this->templateRepository = $templateRepository;
        $this->parent = $parent;
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function show()
    {
        $templates = $this->templateRepository->findByDeleted(false);

        return $this->render(
            'OpenOrchestraBackofficeBundle:Tree:showGSTreeTemplates.html.twig',
            array(
                'templates' => $templates
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'GStemplates';
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_TREE_TEMPLATE;
    }
}
