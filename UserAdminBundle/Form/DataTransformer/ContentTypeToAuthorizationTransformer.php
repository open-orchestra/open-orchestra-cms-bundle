<?php

namespace OpenOrchestra\UserAdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\UserBundle\Document\Authorization;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelBundle\Repository\ContentTypeRepository;
use OpenOrchestra\UserBundle\Model\AuthorizationInterface;

/**
 * Class ContentTypeToAuthorizationTransformer
 */
class ContentTypeToAuthorizationTransformer implements DataTransformerInterface
{

    /**
     * @param ContentTypeRepository $contentTypeRepository
     */
    public function __construct(ContentTypeRepository $contentTypeRepository, $authorizationClass)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->authorizationClass = $authorizationClass;
    }

    /**
     * @param array $user
     *
     * @return string
     */
    public function transform($user)
    {
        $contentTypes = $this->contentTypeRepository->findAllByDeletedInLastVersion();
        $contentTypes[AuthorizationInterface::NODE] = null;
        $authorizations = $user->getAuthorizations();
        foreach ($authorizations as $authorization) {
            if (!array_key_exists($authorization->getName(), $contentTypes)) {
                $user->removeAuthorization($authorization);
                continue;
            }
            unset($contentTypes[$authorization->getName()]);
        }
        $authorizationClass = $this->authorizationClass;
        foreach($contentTypes as $key => $contentType){
            $authorization = new $authorizationClass();
            $authorization->setName($key);
            $user->addAuthorization($authorization);
        }

        return $user;
    }

    /**
     * @param string $user
     *
     * @return array
     */
    public function reverseTransform($user)
    {
        return $user;
    }
}
