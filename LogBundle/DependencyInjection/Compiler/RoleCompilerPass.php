<?php

namespace OpenOrchestra\LogBundle\DependencyInjection\Compiler;

use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\AbstractRoleCompilerPass;
use OpenOrchestra\LogBundle\NavigationPanel\Strategies\LogPanelStrategy;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RoleCompilerPass
 */
class RoleCompilerPass extends AbstractRoleCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this->addRoles($container, array(
            LogPanelStrategy::ROLE_ACCESS_LOG,
        ));

        if ($container->hasParameter('open_orchestra_backoffice.role')) {
            $param = $container->getParameter('open_orchestra_backoffice.role');
            if ($container->hasParameter('open_orchestra_log.role')) {
                $param = array_merge_recursive($param, $container->getParameter('open_orchestra_log.role'));
            }
            $container->setParameter('open_orchestra_backoffice.role', $param);
        }
    }
}
