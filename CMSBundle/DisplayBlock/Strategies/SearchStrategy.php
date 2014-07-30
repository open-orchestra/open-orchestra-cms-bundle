<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;

use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\CMSBundle\Form\Type\AutocompleteSearchType;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class SearchStrategy
 */
class SearchStrategy extends AbstractStrategy
{
    protected $formFactory;
    protected $router;

    /**
     * @param FormFactory           $formFactory
     * @param UrlGeneratorInterface $router
     */
    public function __construct(FormFactory $formFactory, UrlGeneratorInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * Check if the strategy support this block
     *
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::SEARCH == $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function show(BlockInterface $block)
    {
        $attributes = $block->getAttributes();
        $value = $attributes['value'];
        $class = $attributes['class'];
        $nodeId = $attributes['nodeId'];
        $limit = 7;//$attributes['limit'];

        // Search form
        $form = $this->generateSearchForm($value, $class, $nodeId, $limit);

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Search:show.html.twig',
            array(
                'form' => $form->createView(),
                'url' => 'php_orchestra_autocomplete'
            )
        );
    }

    /**
     * Perform the show action for a block on the backend
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function showBack(BlockInterface $block)
    {
        $attributes = $block->getAttributes();
        $value = $attributes['value'];
        $class = $attributes['class'];
        $nodeId = $attributes['nodeId'];
        $limit = $attributes['limit'];

        $form = $this->generateSearchForm($value, $class, $nodeId, $limit);

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Search:showBack.html.twig',
            array(
                'form'   => $form->createView(),
            )
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'search';
    }

    /**
     * @param string $value
     * @param string $class
     * @param string $nodeId
     * @param int    $limit
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function generateSearchForm($value, $class, $nodeId, $limit)
    {
        $form = $this->formFactory->create(
            new AutocompleteSearchType(
                $this->router->generate('php_orchestra_autocomplete', array('limit' => $limit)),
                $value,
                $class
            ),
            null,
            array(
                'action' => $this->router->generate('php_orchestra_cms_node', array('nodeId' => $nodeId)),
                'method' => 'GET',
            )
        );

        return $form;
    }
}
