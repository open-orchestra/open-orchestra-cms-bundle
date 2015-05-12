<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\WorkflowAdminBundle\Facade\FonctionCollectionFacade;

/**
 * Class FonctionCollectionTransformer
 */
class FonctionCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new FonctionCollectionFacade();

        foreach ($mixed as $fonction) {
            $facade->addFonction($this->getTransformer('fonction')->transform($fonction));
        }

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_fonction_new',
            array()
        ));

        $facade->addLink('_translate', $this->generateRoute(
            'open_orchestra_api_translate'
        ));

        return $facade;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'fonction_collection';
    }
}
