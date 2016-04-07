<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use Symfony\Component\Translation\TranslatorInterface;

@trigger_error('The '.__NAMESPACE__.'\AbstractNavigationPanelStrategy class is deprecated since version 1.1.0 and will be removed in 1.2.0, use AbstractNavigationStrategy', E_USER_DEPRECATED);

/**
 * Class AbstractNavigationPanelStrategy
 *
 * @deprecated use AbstractNavigationStrategy, will be removed in 1.2.0
 */
abstract class AbstractNavigationPanelStrategy extends AbstractNavigationStrategy
{
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
        parent::__construct($name, $weight, $parent, $role, $datatableParameter, $translator);
    }
}
