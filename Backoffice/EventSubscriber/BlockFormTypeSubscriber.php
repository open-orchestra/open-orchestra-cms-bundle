<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Util\Inflector;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Class BlockFormTypeSubscriber
 */
class BlockFormTypeSubscriber implements EventSubscriberInterface
{
    protected $fixedParameters;

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'submit'
        );
    }

    /**
     * @param array $fixedParameters
     */
    public function __construct(array $fixedParameters)
    {
        $this->fixedParameters = $fixedParameters;
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $block = $event->getForm()->getData();
        if (null !== $block) {
            $blockAttributes = array();
            $blockAttributes = $this->getBlockAttributes($event->getForm(), $block, $blockAttributes);

            $block->setAttributes($blockAttributes);
        }
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     * @param array          $blockAttributes
     *
     * @return array
     */
    protected function getBlockAttributes(
        FormInterface $form,
        BlockInterface $block,
        array $blockAttributes
    ) {
        foreach ($form->all() as $key => $children) {
            if ($children->count() > 0) {
                $blockAttributes = $this->getBlockAttributes($children, $block, $blockAttributes);
            } else {
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
        }

        return $blockAttributes;
    }
}
