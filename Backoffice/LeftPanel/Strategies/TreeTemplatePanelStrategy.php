<?php

namespace PHPOrchestra\Backoffice\LeftPanel\Strategies;

use PHPOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;

/**
 * Class TreeTemplatePanel
 */
class TreeTemplatePanelStrategy extends AbstractLeftPaneStrategy
{
    /**
     * @var TemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @param TemplateRepositoryInterface $templateRepository
     */
    public function __construct(TemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * @return string
     */
    public function show()
    {
        $templates = $this->templateRepository->findByDeleted(false);

        return $this->render(
            'PHPOrchestraBackofficeBundle:Tree:showTreeTemplates.html.twig',
            array(
                'templates' => $templates
            )
        );
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
        return 'templates';
    }
}
