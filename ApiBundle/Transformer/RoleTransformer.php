<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\RoleFacade;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class RoleTransformer
 */
class RoleTransformer extends AbstractTransformer
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
     * @param RoleInterface $mixed
     *
     * @return RoleFacade
     */
    public function transform($mixed)
    {
        $facade = new RoleFacade();

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();
        $facade->description = $this->translationChoiceManager->choose($mixed->getDescriptions());
        $facade->fromStatus = $mixed->getFromStatus();
        $facade->toStatus = $mixed->getToStatus();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_role_show',
            array('roleId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_role_delete',
            array('roleId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_role_form',
            array('roleId' => $mixed->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'role';
    }
}
