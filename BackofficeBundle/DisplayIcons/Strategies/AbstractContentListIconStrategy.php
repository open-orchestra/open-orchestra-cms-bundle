<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcons\Strategies;

/**
 * Class AbstractContentListIconStrategy
 */
abstract class AbstractContentListIconStrategy extends AbstractStrategy
{
    /**
     * Perform the show action for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('PHPOrchestraBackofficeBundle:Block/ContentList:showIcon.html.twig');
    }
}
