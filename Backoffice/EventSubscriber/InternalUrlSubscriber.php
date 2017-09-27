<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelBundle\Document\InternalUrl;
use OpenOrchestra\ModelInterface\Model\InternalUrlInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Class InternalUrlSubscriber
 */
class InternalUrlSubscriber implements EventSubscriberInterface
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
        $form = $event->getForm();
        $data = array_merge(array(
            'siteId' => $form->get('siteId')->getConfig()->getData(),
            'nodeId' => NodeInterface::ROOT_NODE_ID,
            'aliasId' => '',
            'wildcards' => array(),
        ), $event->getData());
        $data = $this->addFormElements($form, $data) ;

        $event->setData($data);
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ('PATCH' !== $form->getRoot()->getConfig()->getMethod()) {
            $data = $event->getData();
            $dataToArray = array();
            if (($data instanceof InternalUrlInterface)) {
                $dataToArray = array(
                    'siteId' => $data->getSiteId(),
                    'nodeId' => $data->getNodeId(),
                    'aliasId' => $data->getAliasId(),
                    'wildcards' => $data->getWildcards(),
                );
            } else {
                $dataToArray = array(
                    'siteId' => $form->get('siteId')->getConfig()->getData(),
                    'nodeId' => NodeInterface::ROOT_NODE_ID,
                    'aliasId' => '',
                    'wildcards' => array(),
                );
            }
            $dataToArray = $this->addFormElements($form, $dataToArray);

            $data = new InternalUrl();
            $data->setSiteId($dataToArray['siteId']);
            $data->setNodeId($dataToArray['nodeId']);
            $data->setAliasId($dataToArray['aliasId']);
            $data->setWildcards($dataToArray['wildcards']);

            $event->setData($data);
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
     * @param FormInterface $form
     * @param array         $data
     */
    protected function addFormElements(FormInterface $form, array $data)
    {
        $site = $this->siteRepository->findOneBySiteId($data['siteId']);

        $form->add('aliasId', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.site_alias',
            'attr' => array('class' => 'subform-to-refresh patch-submit-change'),
            'choices' => $this->getChoices($site),
            'required' => true,
        ));
        $aliases = $form->get('aliasId')->getConfig()->getOption('choices');
        if (!array_key_exists($data['aliasId'], $aliases)) {
            $data['aliasId'] = key($aliases);
        }

        $form->add('nodeId', 'oo_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.node',
            'siteId' => $data['siteId'],
            'attr' => array('class' => 'orchestra-tree-choice subform-to-refresh patch-submit-change'),
            'required' => true,
        ));
        $nodes = $form->get('nodeId')->getConfig()->getOption('choices');
        if (!array_key_exists($data['nodeId'], $nodes)) {
            $data->setNodeId($data['nodeId']);
        }

        $language = $site->getAliases()[$data['aliasId']]->getLanguage();
        $node = $this->nodeRepository->findOnePublished($data['nodeId'], $language, $data['siteId']);

        if (is_null($node)) {
            $node = $this->nodeRepository->findInLastVersion($data['nodeId'], $language, $data['siteId']);
        }
        preg_match_all('/{(.*?)}/', $node->getRoutePattern(), $matches);
        if (is_array($matches) && array_key_exists(1, $matches) && is_array($matches[1])) {
            $wildcards = array_fill_keys($matches[1], '');
            $data['wildcards'] = array_intersect_key($data['wildcards'], $wildcards);
            $data['wildcards'] = array_merge($wildcards, $data['wildcards']);
        } else {
            $data['wildcards'] = array();
        }
        $form->add('wildcards', 'collection', array(
            'entry_type' => 'text',
            'label' => false,
            'attr' => array('class' => 'subform-to-refresh'),
            'data' => $data['wildcards'],
            'entry_options' => array(
                'required' => true,
            ),
        ));

        return $data;
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
