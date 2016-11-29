<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
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
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($redirection)
    {
        if (!$redirection instanceof RedirectionInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $redirection->getId();
        $facade->siteName = $redirection->getSiteName();
        $facade->routePattern = $redirection->getRoutePattern();
        $facade->locale = $redirection->getLocale();
        $facade->redirection = $redirection->getUrl();
        if ($redirection->getNodeId()) {
            $facade->redirection = $redirection->getNodeId();
        }
        $facade->permanent = $redirection->isPermanent();
        $facade->addRight('can_edit', $this->authorizationChecker->isGranted(ContributionActionInterface::EDIT, $redirection));

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
