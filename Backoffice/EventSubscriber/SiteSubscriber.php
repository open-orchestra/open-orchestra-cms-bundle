<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class SiteSubscriber
 */
class SiteSubscriber implements EventSubscriberInterface
{
    protected $siteRepository;
    protected $attributes;

    /**
     * @param SiteRepositoryInterface $siteRepository
     * @param array                   $attributes
     */
    public function __construct(SiteRepositoryInterface $siteRepository, array $attributes)
    {
        $this->siteRepository = $siteRepository;
        $this->attributes = $attributes;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $this->addFormElements($event) ;
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ('PATCH' !== $form->getRoot()->getConfig()->getMethod()) {
            $this->addFormElements($event);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    protected function addFormElements(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $siteId = is_array($data) && array_key_exists('siteId', $data) ? $data['siteId'] : $form->get('siteId')->getConfig()->getData();
        $nodeId = is_array($data) && array_key_exists('nodeId', $data) ? $data['nodeId'] : '';
        $aliasId = is_array($data) && array_key_exists('aliasId', $data) ? $data['aliasId'] : '';

        if ($siteId != '') {
            $form->add('nodeId', 'oo_node_choice', array(
                'label' => 'open_orchestra_backoffice.form.internal_link.node',
                'siteId' => $siteId,
                'attr' => array('class' => 'orchestra-node-choice subform-to-refresh'),
                'required' => true,
            ));
            $form->add('aliasId', 'choice', array(
                'label' => 'open_orchestra_backoffice.form.internal_link.site_alias',
                'attr' => array('class' => 'subform-to-refresh'),
                'choices' => $this->getChoices($siteId),
                'required' => false,
            ));
            if (!array_key_exists($nodeId, $form->get('nodeId')->getConfig()->getOption('choices'))) {
                $data['nodeId'] = '';
            }
            if (!array_key_exists($aliasId, $form->get('aliasId')->getConfig()->getOption('choices'))) {
                $data['aliasId'] = '';
            }
            $event->setData($data);
        }
    }

    /**
     * @param $siteId
     *
     * @return array
     */
    protected function getChoices($siteId)
    {
        $choices = array();
        $site = $this->siteRepository->findOneBySiteId($siteId);
        foreach ($site->getAliases() as $aliasId => $alias) {
            $choices[$aliasId] = $alias->getDomain() . '(' . $alias->getLanguage();
            if ($alias->getPrefix() != '') {
                $choices[$aliasId] .= ' - ' . $alias->getPrefix();
            }
            $choices[$aliasId] .= ')';
        }

        return $choices;
    }
}
