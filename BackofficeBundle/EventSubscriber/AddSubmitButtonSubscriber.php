<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class AddSubmitButtonSubscriber
 */
class AddSubmitButtonSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $parameter = $this->generateParameter($data);
        $form->add('submit', 'submit', $parameter);
    }

    protected function generateParameter($data)
    {
        $parameter = array('label' => 'open_orchestra_base.form.submit', 'attr' => array('class' => 'submit_form'));
        if ($data instanceof StatusableInterface && is_object($data->getStatus()) && $data->getStatus()->isPublished()) {
            $parameter['attr'] = array('class' => 'disabled');
        }

        return $parameter;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
        );
    }
}
