<?php

namespace OpenOrchestra\Backoffice\LeftPanel\Strategies;


/**
 * Class AdministrationPanelStrategy
 */
class AdministrationPanelStrategy extends AbstractLeftPaneStrategy
{
    protected $name;
    protected $weight;
    protected $parent;

    /**
     * @param string $name
     * @param int    $weight
     * @param string $parent
     */
    public function __construct($name, $weight = 0, $parent = self::ADMINISTRATION)
    {
        $this->name = $name;
        $this->weight = $weight;
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:AdministrationPanel:' . $this->name . '.html.twig');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }
}
