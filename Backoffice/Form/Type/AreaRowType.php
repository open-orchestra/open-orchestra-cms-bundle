<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AreaRowType
 */
class AreaRowType extends AbstractAreaType
{
    protected $areaRowSubscriber;

    /**
     * @param string                   $areaClass
     * @param EventSubscriberInterface $areaRowSubscriber
     */
    public function __construct($areaClass, EventSubscriberInterface $areaRowSubscriber)
    {
        parent::__construct($areaClass);
        $this->areaRowSubscriber = $areaRowSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventSubscriber($this->areaRowSubscriber);
    }
}
