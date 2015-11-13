<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class TemplateStrategy
 */
class TemplateStrategy implements AuthorizeEditionInterface
{
    /**
     * @var TemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $autorizationChecker;

    /**
     * @param TemplateRepositoryInterface   $templateRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(TemplateRepositoryInterface $templateRepository, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->templateRepository = $templateRepository;
        $this->autorizationChecker = $authorizationChecker;
    }

    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function support($document)
    {
        return $document instanceof TemplateInterface;
    }

    /**
     * @param TemplateInterface|mixed $document
     *
     * @return bool
     */
    public function isEditable($document)
    {
        $return = false;

        if ($document instanceof TemplateInterface) {
            $return = $this->autorizationChecker->isGranted(TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE);
        }

        return $return;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'template';
    }
}
