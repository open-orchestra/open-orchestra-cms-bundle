<?php

namespace PHPOrchestra\Backoffice\LeftPanel\Strategies;


/**
 * Class AdministrationPanelStrategy
 */
class AdministrationPanelStrategy extends AbstractLeftPaneStrategy
{
    protected $name;
    protected $weight;

    /**
     * @param string $name
     * @param int    $weight
     */
    public function __construct($name, $weight = 0)
    {
        $this->name = $name;
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render('PHPOrchestraBackofficeBundle:AdministrationPanel:' . $this->name . '.html.twig');
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
        return self::ADMINISTRATION;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }
}
