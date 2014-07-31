<?php

namespace PHPOrchestra\CMSBundle\Twig;
use PHPOrchestra\CMSBundle\Manager\TreeManager;


/**
 * Class TreeHelperExtension
 */
class TreeHelperExtension extends \Twig_Extension
{
    protected $manager;

    /**
     * @param TreeManager $manager
     */
    public function __construct(TreeManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $nodes
     *
     * @return array
     */
    public function treeFormatter(array $nodes)
    {
        return $this->manager->generateTree($nodes);
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('tree_formatter', array($this, 'treeFormatter')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'tree';
    }

}
