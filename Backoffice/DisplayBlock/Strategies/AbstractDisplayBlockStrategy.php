<?php

namespace OpenOrchestra\Backoffice\DisplayBlock\Strategies;

use OpenOrchestra\Backoffice\DisplayBlock\DisplayBlockInterface;
use OpenOrchestra\Backoffice\DisplayBlock\DisplayBlockManager;

/**
 * Class AbstractDisplayBlockStrategy
 */
abstract class AbstractDisplayBlockStrategy implements DisplayBlockInterface
{
    /**
     * @var DisplayBlockManager
     */
    protected $manager;

    /**
     * @param DisplayBlockManager $manager
     */
    public function setManager(DisplayBlockManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function render($view, array $parameters = array())
    {
        return $this->manager->getTemplating()->render($view, $parameters);
    }
}
