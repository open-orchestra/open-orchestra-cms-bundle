<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\StatusFacade;
use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;
use PHPOrchestra\ModelBundle\Model\StatusInterface;

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
        $facade->initial = $mixed->isInitial();
        $facade->label = $this->translationChoiceManager->choose($mixed->getLabels());
        $facade->toRole = $mixed->getToRole();
        $facade->fromRole = $mixed->getFromRole();

        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_status_delete',
            array('statusId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_backoffice_status_form',
            array('statusId' => $mixed->getId())
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
