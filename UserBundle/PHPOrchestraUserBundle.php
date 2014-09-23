<?php

namespace PHPOrchestra\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class PHPOrchestraUserBundle
 */
class PHPOrchestraUserBundle extends Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
