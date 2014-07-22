<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;

use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\CMSBundle\Form\Type\ContactType;
use PHPOrchestra\CMSBundle\Model\Block;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContactStrategy
 */
class ContactStrategy extends AbstractStrategy
{
    protected $formFactory;

    /**
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Check if the strategy support this block
     *
     * @param Block $block
     *
     * @return boolean
     */
    public function support(Block $block)
    {
        return DisplayBlockInterface::CONTACT == $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param Block $block
     *
     * @return Response
     */
    public function show(Block $block)
    {
        $form = $this->formFactory->create(new ContactType());

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Contact:show.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Perform the show action for a block on the backend
     *
     * @param Block $block
     *
     * @return Response
     */
    public function showBack(Block $block)
    {
        $form = $this->formFactory->create(new ContactType());

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Contact:showBack.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }

} 