<?php

namespace OpenOrchestra\Backoffice\Context;

use FOS\UserBundle\Model\GroupableInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ContextBackOfficeManager
 */
class ContextBackOfficeManager implements ContextBackOfficeInterface
{
    protected $siteId;
    protected $session;
    protected $tokenStorage;
    protected $currentLanguage;
    protected $currentSiteLanguages = array();
    protected $defaultLocale;
    protected $siteRepository;
    protected $authorizationChecker;

    /**
     * Constructor
     *
     * @param Session                       $session
     * @param TokenStorageInterface         $tokenStorage
     * @param string                        $defaultLocale
     * @param SiteRepositoryInterface       $siteRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Session $session,
        TokenStorageInterface $tokenStorage,
        $defaultLocale,
        SiteRepositoryInterface $siteRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->defaultLocale = $defaultLocale;
        $this->siteRepository = $siteRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Get current back office language
     *
     * @return string
     */
    public function getBackOfficeLanguage()
    {
        $currentLanguage = $this->session->get(self::KEY_LOCALE);

        if (!$currentLanguage) {
            $currentLanguage = $this->defaultLocale;
            $token = $this->tokenStorage->getToken();
            if ($token && ($user = $token->getUser()) instanceof UserInterface) {
                if (null !== $user->getLanguage()) {
                    $currentLanguage = $user->getLanguage();
                }
                $this->setBackOfficeLanguage($currentLanguage);
            }
        }

        return $currentLanguage;

    }

    /**
     * Get availables sites on platform
     *
     * @return array<SiteInterface>
     */
    public function getAvailableSites()
    {
        $sites = array();
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            if ($this->authorizationChecker->isGranted(ContributionRoleInterface::PLATFORM_ADMIN)) {
                return $this->siteRepository->findByDeleted(false);
            }

            if (($user = $token->getUser()) instanceof GroupableInterface) {
                foreach ($user->getGroups() as $group) {
                    /** @var SiteInterface $site */
                    $site = $group->getSite();
                    if (null !== $site && !$group->isDeleted() && !$site->isDeleted()) {
                        $sites[$site->getId()] = $site;
                    }
                }
            }
        }

        return $sites;
    }

    /**
     * Get the current site id
     *
     * @return string
     */
    public function getSiteId()
    {
        if (is_null($this->siteId)) {
            $this->siteId = $this->getSite()['siteId'];
        }

        return $this->siteId;
    }

    /**
     * Get the current domain
     *
     * @return string
     */
    public function getSiteName()
    {
        $site = $this->getSite();

        return $site['name'];
    }

    /**
     * Get the default language of the current site
     *
     * @return string
     */
    public function getSiteDefaultLanguage()
    {
        if (is_null($this->currentLanguage)) {
            $this->currentLanguage = $this->getSite()['defaultLanguage'];
        }

        return $this->currentLanguage;
    }

    /**
     * Get languages of the current site
     *
     * @return array
     */
    public function getSiteLanguages()
    {
        if (empty($this->currentSiteLanguages)) {
            $this->currentSiteLanguages = $this->getSite()['languages'];
        }

        return $this->currentSiteLanguages;
    }

    /**
     * Get the contribution language setted by the user for the current site
     *
     * @return string
     */
    public function getSiteContributionLanguage()
    {
        $currentLanguage = $this->getSite()['defaultLanguage'];

        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            if (($user = $token->getUser()) instanceof UserInterface && $user->hasLanguageBySite($this->getSiteId())) {
                $currentLanguage = $user->getLanguageBySite($this->getSiteId());
            }
        }

        return $currentLanguage;
    }

    /**
     * Clear saved context
     */
    public function clearContext()
    {
        $this->session->remove(self::KEY_SITE);
        $this->session->remove(self::KEY_LOCALE);
        $this->tokenStorage->getToken()->setAuthenticated(false);
    }

    /**
     * Set current back office language
     *
     * @param string $language
     */
    public function setBackOfficeLanguage($language)
    {
        $this->session->set(self::KEY_LOCALE, $language);
    }

    /**
     * Set current site
     *
     * @param string $siteId
     * @param string $siteName
     * @param string $siteDefaultLanguage
     * @param array $languages
     */
    public function setSite($siteId, $siteName, $siteDefaultLanguage, array $languages)
    {
        $this->siteId = $siteId;
        $this->session->set(
            self::KEY_SITE,
            array(
                'siteId' => $siteId,
                'name' => $siteName,
                'defaultLanguage' => $siteDefaultLanguage,
                'languages' => $languages,
            )
        );
    }

    /**
     * Get current selected site
     *
     * @return array
     */
    protected function getSite()
    {
        $currentSite = $this->session->get(self::KEY_SITE);

        if (!$currentSite || (is_integer($currentSite['siteId']) && $currentSite['siteId'] == 0)) {
            $sites = $this->getAvailableSites();
            if (count($sites) > 0) {
                $site = array_shift($sites);
                $siteId = $site->getSiteId();
                $siteName = $site->getName();
                $locale = $site->getDefaultLanguage();
                $languages = $site->getLanguages();
                $this->setSite($siteId, $siteName, $locale, $languages);
                $currentSite = $this->session->get(self::KEY_SITE);
            }
        }

        return $currentSite;
    }
}
