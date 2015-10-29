<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\RedirectionFacade;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;

/**
 * Class RedirectionTransformer
 */
class RedirectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param RedirectionInterface $redirection
     *
     * @return RedirectionFacade
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($redirection)
    {
        if (!$redirection instanceof RedirectionInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new RedirectionFacade();

        $facade->id = $redirection->getId();
        $facade->siteName = $redirection->getSiteName();
        $facade->routePattern = $redirection->getRoutePattern();
        $facade->locale = $redirection->getLocale();
        $facade->redirection = $redirection->getUrl();
        if ($redirection->getNodeId()) {
            $facade->redirection = $redirection->getNodeId();
        }
        $facade->permanent = $redirection->isPermanent();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_redirection_show',
            array('redirectionId' => $redirection->getId())
        ));

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_REDIRECTION)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_redirection_delete',
                array('redirectionId' => $redirection->getId())
            ));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_REDIRECTION)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_redirection_form',
                array('redirectionId' => $redirection->getId())
            ));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'redirection';
    }
}
