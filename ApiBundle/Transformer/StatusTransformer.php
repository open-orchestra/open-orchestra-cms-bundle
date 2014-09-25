<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\StatusFacade;
use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;
use PHPOrchestra\ModelBundle\Model\StatusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class StatusTransformer
 */
class StatusTransformer extends AbstractTransformer
{
    protected $translationChoiceManager;

    /**
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(TranslationChoiceManager $translationChoiceManager)
    {
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param StatusInterface $mixed
     *
     * @return FacadeInterface|StatusFacade
     */
    public function transform($mixed)
    {
        $facade = new StatusFacade();

        $facade->published = $mixed->isPublished();
        $facade->initial = $mixed->getInitial();
        $facade->initial = implode(',', $mixed->getInitial());
        $facade->label = $this->translationChoiceManager->choose($mixed->getLabels());
        $facade->role = $mixed->getRole();

        $facade->addLink('_self_delete', $this->getRouter()->generate(
            'php_orchestra_api_status_delete',
            array('statusId' => $mixed->getId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        $facade->addLink('_self_form', $this->getRouter()->generate(
            'php_orchestra_backoffice_status_form',
            array('statusId' => $mixed->getId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'status';
    }
}
