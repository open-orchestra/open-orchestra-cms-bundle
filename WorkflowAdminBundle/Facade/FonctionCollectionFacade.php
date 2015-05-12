<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class FonctionCollectionFacade
 */
class FonctionCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'fonctions';

    /**
     * @Serializer\Type("array<OpenOrchestra\WorkflowAdminBundle\Facade\FonctionFacade>")
     */
    public $fonctions = array();

    /**
     * @param FacadeInterface $log
     */
    public function addFonction(FacadeInterface $fonction)
    {
        $this->fonctions[] = $fonction;
    }

    /**
     * @return mixed
     */
    public function getFonctions()
    {
        return $this->fonctions;
    }
}
