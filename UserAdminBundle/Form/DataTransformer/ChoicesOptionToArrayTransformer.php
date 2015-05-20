<?php

namespace OpenOrchestra\UserAdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\UserBundle\Document\Authorization;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ChoicesOptionToArrayTransformer
 */
class ChoicesOptionToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param array $user
     *
     * @return string
     */
    public function transform($user)
    {
        $newAuthorizations = new ArrayCollection();
        $authorizations = $user->getAuthorizations();
        $user->resetAuthorizations();
        foreach($authorizations as $authorization){
            $newAuthorizations[] = $authorization->getWorkflowFunctions();
        }
        $user->setAuthorizations($newAuthorizations);
        return $user;
    }

    /**
     * @param string $user
     *
     * @return array
     */
    public function reverseTransform($user)
    {
        $authorizations = $user->getAuthorizations();
        $user->resetAuthorizations();
        foreach($authorizations as $authorization){
            $newAuthorization = new Authorization();
            $newAuthorization->setWorkflowFunctions($authorization);
            $user->addAuthorization($newAuthorization);
        }
        return $user;
    }
}
