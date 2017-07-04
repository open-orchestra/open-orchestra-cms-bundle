<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class RedirectionTransformer
 */
class RedirectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $redirectionRepository;

    /**
     * @param string                         $facadeClass
     * @param AuthorizationCheckerInterface  $authorizationChecker
     * @param RedirectionRepositoryInterface $redirectionRepository
     */
    public function __construct(
        $facadeClass,
        AuthorizationCheckerInterface $authorizationChecker,
        RedirectionRepositoryInterface $redirectionRepository
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->redirectionRepository = $redirectionRepository;
    }

    /**
     * @param RedirectionInterface $redirection
     * @param array                $params
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($redirection, array $params = array())
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
        $facade->addRight('can_delete', $this->authorizationChecker->isGranted(ContributionActionInterface::DELETE, $redirection));

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return mixed
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        if (null !== $facade->id) {
            return $this->redirectionRepository->find($facade->id);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'redirection';
    }
}
