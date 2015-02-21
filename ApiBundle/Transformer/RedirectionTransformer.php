<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\RedirectionFacade;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;

/**
 * Class RedirectionTransformer
 */
class RedirectionTransformer extends AbstractTransformer
{
    /**
     * @param RedirectionInterface $mixed
     *
     * @return RedirectionFacade
     */
    public function transform($mixed)
    {
        $facade = new RedirectionFacade();

        $facade->id = $mixed->getId();
        $facade->siteName = $mixed->getSiteName();
        $facade->routePattern = $mixed->getRoutePattern();
        $facade->locale = $mixed->getLocale();
        $facade->redirection = $mixed->getUrl();
        if ($mixed->getNodeId()) {
            $facade->redirection = $mixed->getNodeId();
        }
        $facade->permanent = $mixed->isPermanent();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_redirection_show',
            array('redirectionId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_redirection_delete',
            array('redirectionId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_redirection_form',
            array('redirectionId' => $mixed->getId())
        ));

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
