<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\ModelInterface\Helper\SuppressSpecialCharacterHelperInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class KeywordToDocumentManager
 */
class KeywordToDocumentManager
{
    /**
     * @param KeywordRepositoryInterface              $keywordRepository
     * @param SuppressSpecialCharacterHelperInterface $suppressSpecialCharacterHelper
     * @param string                                  $keywordClass
     * @param AuthorizationCheckerInterface           $authorizationChecker
     */
    public function __construct(
        KeywordRepositoryInterface $keywordRepository,
        SuppressSpecialCharacterHelperInterface $suppressSpecialCharacterHelper,
        $keywordClass,
        AuthorizationCheckerInterface $authorizationChecker
    ){
        $this->keywordRepository = $keywordRepository;
        $this->suppressSpecialCharacterHelper = $suppressSpecialCharacterHelper;
        $this->keywordClass = $keywordClass;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param string $keyword
     *
     * @return KeywordInterface
     */
    public function getDocument($keyword)
    {
        $keyword = $this->suppressSpecialCharacterHelper->transform($keyword);
        $keywordClass = $this->keywordClass;
        $keywordEntity = $this->keywordRepository->findOneByLabel($keyword);

        if (is_null($keywordEntity)
            && !$this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, KeywordInterface::ENTITY_TYPE)
        ) {
            throw new AccessDeniedHttpException();
        }
        if (is_null($keywordEntity)) {
            $keywordEntity = new $keywordClass();
            $keywordEntity->setLabel($keyword);
            $this->keywordRepository->getManager()->persist($keywordEntity);
            $this->keywordRepository->getManager()->flush($keywordEntity);
        }

        return $keywordEntity;
    }
}
