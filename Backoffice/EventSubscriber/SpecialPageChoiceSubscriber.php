<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class SpecialPageChoiceSubscriber
 */
class SpecialPageChoiceSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;
    protected $specialPageList;
    protected $contextManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository,
     * @param CurrentSiteIdInterface  $contextManager,
     * @param array                   $specialPageList
     */
     public function __construct(
         NodeRepositoryInterface $nodeRepository,
         CurrentSiteIdInterface $contextManager,
         array $specialPageList
    ) {
         $this->nodeRepository = $nodeRepository;
         $this->contextManager = $contextManager;
         $this->specialPageList = $specialPageList;
     }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $form->add('specialPageName', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.node.specialPageName',
            'choices' => $this->getSpecialPageList($data),
            'group_id' => 'properties',
            'sub_group_id' => 'properties',
            'required' => false,
        ));
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    /**
     * @param NodeInterface|null $node
     *
     * @return array
     */
    protected function getSpecialPageList($node)
    {
        $siteId = $this->contextManager->getCurrentSiteId();
        $language = $this->contextManager->getCurrentSiteDefaultLanguage();
        $specialPages = $this->nodeRepository->findAllSpecialPage($language, $siteId);

        $specialPageList = $this->specialPageList;
        foreach ($specialPages as $specialPage) {
            if (($node instanceof NodeInterface &&
                $specialPage->getSpecialPageName() !== $node->getSpecialPageName()) ||
                is_null($node)
            ) {
                unset($specialPageList[$specialPage->getSpecialPageName()]);
            }
        }

        return $specialPageList;
    }
}
