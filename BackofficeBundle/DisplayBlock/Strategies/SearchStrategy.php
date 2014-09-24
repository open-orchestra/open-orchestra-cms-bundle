<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\IndexationBundle\Form\Type\AutocompleteSearchType;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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
        $limit = (array_key_exists('limit', $attributes)?$attributes['limit']:7);

        return $this->render(
            'PHPOrchestraBackofficeBundle:Block/Search:show.html.twig',
            array(
                'value' => $value,
                'limit' => $limit
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
     * @return Form
     */
    protected function generateSearchForm($value, $class, $nodeId, $limit)
    {
        try {
            $action = $this->router->generate('php_orchestra_front_node', array('nodeId' => $nodeId));
        } catch (RouteNotFoundException $e) {
            $action = null;
        }

        $form = $this->formFactory->create(
            new AutocompleteSearchType(
                $this->router->generate('php_orchestra_indexation_autocomplete', array('limit' => $limit)),
                $value,
                $class
            ),
            null,
            array(
                'action' => $action,
                'method' => 'GET',
            )
        );

        return $form;
    }
}
