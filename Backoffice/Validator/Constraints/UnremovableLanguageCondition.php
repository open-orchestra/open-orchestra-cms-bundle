<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Validator\Constraint;

/**
 * Class UnremovableLanguageCondition
 */
class UnremovableLanguageCondition extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.website.unremovable_language';

    protected $aliases;
    protected $languages;

    /**
     * @param SiteInterface $site
     */
    public function __construct(SiteInterface $site)
    {
        $this->aliases = clone $site->getAliases();
        $this->languages = $site->getLanguages();
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'unremovable_language';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
