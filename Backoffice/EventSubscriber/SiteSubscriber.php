<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
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
    protected $nodeRepository;
    protected $attributes;

    /**
     * @param SiteRepositoryInterface $siteRepository
     * @param NodeRepositoryInterface $nodeRepository
     * @param array                   $attributes
     */
    public function __construct(
        SiteRepositoryInterface $siteRepository,
        NodeRepositoryInterface $nodeRepository,
        array $attributes
    ) {
        $this->siteRepository = $siteRepository;
        $this->nodeRepository = $nodeRepository;
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
            $site = $this->siteRepository->findOneBySiteId($siteId);
            $form->add('aliasId', 'choice', array(
                'label' => 'open_orchestra_backoffice.form.internal_link.site_alias',
                'attr' => array('class' => 'subform-to-refresh patch-submit-change'),
                'choices' => $this->getChoices($site),
                'required' => true,
            ));
            $aliases = $form->get('aliasId')->getConfig()->getOption('choices');
            if (!array_key_exists($aliasId, $aliases)) {
                $data['aliasId'] = key($aliases);
            }
            $form->add('nodeId', 'oo_node_choice', array(
                'label' => 'open_orchestra_backoffice.form.internal_link.node',
                'siteId' => $siteId,
                'attr' => array('class' => 'orchestra-node-choice subform-to-refresh patch-submit-change'),
                'required' => true,
            ));
            $nodes = $form->get('nodeId')->getConfig()->getOption('choices');
            if (!array_key_exists($nodeId, $nodes)) {
                $data['nodeId'] = key($nodes);
            }

            $node = $this->nodeRepository->findOnePublished($data['nodeId'], $site->getAliases()[$data['aliasId']]->getLanguage(), $siteId);

            if (is_null($node)) {
                $node = $this->nodeRepository->findInLastVersion($data['nodeId'], $site->getAliases()[$data['aliasId']]->getLanguage(), $siteId);
            }

            preg_match_all('/{(.*?)}/', $node->getRoutePattern(), $matches);

            if (is_array($matches) && array_key_exists(1, $matches) && is_array($matches[1])) {
                $result = array();
                foreach ($matches[1] as $wildcard) {
                    $result[$wildcard] = array_key_exists('wildcard', $data) && array_key_exists($wildcard, $data['wildcard']) ? $data['wildcard'][$wildcard] : '';
                }
                $data['wildcard'] = $result;
                $form->add('wildcard', 'collection', array(
                    'entry_type' => 'text',
                    'label' => 'open_orchestra_backoffice.form.internal_link.wildcard',
                    'attr' => array('class' => 'subform-to-refresh'),
                    'data' => $data['wildcard'],
                    'entry_options' => array(
                        'required' => true,
                    ),
                ));
            }

            $event->setData($data);
        }
    }

    /**
     * @param $siteId
     *
     * @return array
     */
    protected function getChoices($site)
    {
        $choices = array();
        $index = array();

        foreach ($site->getAliases() as $aliasId => $alias) {
            $choices[$aliasId] = $alias->getDomain() . '(' . $alias->getLanguage();
            if ($alias->getPrefix() != '') {
                $choices[$aliasId] .= ' - ' . $alias->getPrefix();
            }
            $choices[$aliasId] .= ')';
            if ($alias->isMain()) {
                array_unshift($index, $aliasId);
            } else {
                $index[] = $aliasId;
            }
        }

        return array_merge(array_flip($index), $choices);
    }
}
