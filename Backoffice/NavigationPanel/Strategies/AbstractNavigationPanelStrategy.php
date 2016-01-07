<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\Backoffice\NavigationPanel\NavigationPanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractNavigationPanelStrategy
 */
abstract class AbstractNavigationPanelStrategy implements NavigationPanelInterface
{
    const ROOT_MENU = 'root_menu';

    /**
     * @var EngineInterface
     */
    protected $templating;

    protected $name;
    protected $parent;
    protected $weight = 0;
    protected $role = null;
    protected $datatableParameter;
    protected $translator = null;

    /**
     * @param string                   $name
     * @param string                   $role
     * @param int                      $weight
     * @param string                   $parent
     * @param array                    $datatableParameter
     * @param TranslatorInterface|null $translator
     */
    public function __construct($name, $role, $weight, $parent, array $datatableParameter = array(), $translator = null)
    {
        $this->name = $name;
        $this->role = $role;
        $this->weight = $weight;
        $this->parent = $parent;
        $this->translator = $translator;
        $this->datatableParameter = $datatableParameter;
    }

    /**
     * @param EngineInterface $templating
     */
    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @return array
     */
    public function getDatatableParameter()
    {
        if ($this->translator instanceof TranslatorInterface) {
            $this->datatableParameter = $this->preFormatDatatableParameter($this->datatableParameter, $this->translator);
        }

        return array($this->name => $this->datatableParameter);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * return string
     */
    public function getParent()
    {
        if (!is_null($this->parent)) {
            return $this->parent;
        }

        return self::ROOT_MENU;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Renders a view.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     *
     * @return string
     */
    protected function render($view, array $parameters = array())
    {
        return $this->templating->render($view, $parameters);
    }

    /**
     * @param array               $datatableParameter
     * @param TranslatorInterface $translator
     *
     * @return array
     */
    protected function preFormatDatatableParameter(array $datatableParameter, TranslatorInterface $translator)
    {
        foreach ($datatableParameter as $index => $parameters) {
            foreach ($parameters as $name => $parameter) {
                $datatableParameter[$index][$name] = $translator->trans($parameter);
            }
        }

        return $datatableParameter;
    }
}
