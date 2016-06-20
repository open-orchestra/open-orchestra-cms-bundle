<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Util\Inflector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlockTypeSubscriber
 */
class BlockTypeSubscriber implements EventSubscriberInterface
{
    protected $fixedParameters;

    /**
     * @param array $fixedParameters
     */
    public function __construct(array $fixedParameters)
    {
        $this->fixedParameters = $fixedParameters;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'submit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $block = $event->getForm()->getData();
        if (null !== $block) {
            $blockAttributes = array();
            foreach ($event->getForm()->all() as $key => $children) {
                $value = $children->getData();
                if (in_array($key, $this->fixedParameters)) {
                    $setter = 'set' . Inflector::classify($key);
                    $block->$setter($value);
                    continue;
                }
                if (is_string($value) && is_array(json_decode($value, true))) {
                    $value = json_decode($value, true);
                }
                $blockAttributes[$key] = $value;
            }
            $block->setAttributes($blockAttributes);
        }
    }
}
