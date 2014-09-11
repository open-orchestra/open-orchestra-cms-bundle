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
        $facade->label = $this->translationChoiceManager->choose($mixed->getLabels());

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
