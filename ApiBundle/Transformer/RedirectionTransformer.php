<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\RedirectionFacade;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class RedirectionTransformer
 */
class RedirectionTransformer extends AbstractTransformer
{
    /**
     * @param \OpenOrchestra\ModelInterface\Model\RedirectionInterface $redirection
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
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_redirection_delete',
            array('redirectionId' => $redirection->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_redirection_form',
            array('redirectionId' => $redirection->getId())
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
