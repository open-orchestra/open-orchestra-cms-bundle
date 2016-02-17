<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\AreaFlexRowSubscriber;
use OpenOrchestra\Backoffice\Manager\AreaFlexManager;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AreaFlexType
 */
class AreaFlexRowType extends AbstractAreaFlexType
{
    protected $areaFlexManager;

    /**
     * @param string              $areaClass
     * @param AreaFlexManager     $areaFlexManager
     */
    public function __construct($areaClass, AreaFlexManager $areaFlexManager)
    {
        parent::__construct($areaClass);
        $this->areaFlexManager = $areaFlexManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventSubscriber(new AreaFlexRowSubscriber($this->areaFlexManager));
    }
}
