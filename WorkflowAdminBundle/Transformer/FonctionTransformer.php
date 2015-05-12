<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\WorkflowAdminBundle\Facade\FonctionFacade;
use OpenOrchestra\Fonction\Model\FonctionInterface;

/**
 * Class FonctionTransformer
 */
class FonctionTransformer extends AbstractTransformer
{
    /**
     * @param FonctionInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new FonctionFacade();

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_fonction_show',
            array('fonctionId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_fonction_delete',
            array('fonctionId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_fonction_form',
            array('fonctionId' => $mixed->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fonction';
    }
}
