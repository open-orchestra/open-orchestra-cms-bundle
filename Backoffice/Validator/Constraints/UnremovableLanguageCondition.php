<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Validator\Constraint;

/**
 * Class UnremovableLanguageCondition
 */
class UnremovableLanguageCondition extends Constraint implements UnremovableLanguageConditionInterface
{
    public $message = 'open_orchestra_backoffice_validators.website.unremovable_language';

    protected $languages;

    /**
     * @param SiteInterface $site
     */
    public function __construct(SiteInterface $site)
    {
        $this->languages = $site->getLanguages();
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
