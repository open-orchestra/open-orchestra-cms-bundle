<?php

namespace PHPOrchestra\BackofficeBundle\Twig;

use PHPOrchestra\BackOfficeBundle\DisplayIcons\DisplayIconManager;

class BlockIconExtension extends \Twig_Extension
{
    protected $displayIconManager;

    /**
     * @param DisplayIconManager $displayIconManager
     */
    public function __construct(DisplayIconManager $displayIconManager)
    {
        $this->displayIconManager = $displayIconManager;
    }

    /**
     * @param string $block
     *
     * @return string
     */
    public function displayIcon($block)
    {
        return $this->displayIconManager->show($block);
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('display_icon', array($this, 'displayIcon'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'block_icon';
    }
} 