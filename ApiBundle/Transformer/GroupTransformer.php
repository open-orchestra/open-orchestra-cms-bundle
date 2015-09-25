<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\GroupFacade;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class GroupTransformer
 */
class GroupTransformer extends AbstractTransformer
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
     * @param GroupInterface $group
     *
     * @return GroupFacade
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($group)
    {
        if (!$group instanceof GroupInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new GroupFacade();

        $facade->id = $group->getId();
        $facade->name = $group->getName();
        $facade->label = $this->translationChoiceManager->choose($group->getLabels());

        foreach ($group->getRoles() as $role) {
            $facade->addRole($role);
        }
        if ($site = $group->getSite()) {
            $facade->site = $this->getTransformer('site')->transform($site);
        }


        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_group_show',
            array('groupId' => $group->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_group_delete',
            array('groupId' => $group->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_group_form',
            array('groupId' => $group->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
