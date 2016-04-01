<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AbstractAreaContainerType
 */
abstract class AbstractAreaContainerType extends AbstractType
{

    protected $areaContainerRepository;

    /**
     * @param ObjectManager $objectManager
     */
    protected function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }


    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildAreaListView(FormView $view, FormInterface $form, array $options)
    {
        $areaContainer = $view->vars['value'];
        $this->objectManager->refresh($areaContainer);
        $view->vars['areas'] = array();
        $areas = $areaContainer->getAreas();
        foreach ($areas as $area) {
            $view->vars['areas'][] = $area->getAreaId();
        }
    }

}
