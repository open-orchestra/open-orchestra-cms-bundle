<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class SiteCollectionTransformer
 */
class SiteCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $siteCollection
     *
     * @return FacadeInterface
     */
    public function transform($siteCollection)
    {
        $facade = $this->newFacade();

        foreach ($siteCollection as $site) {
            $facade->addSite($this->getTransformer('site')->transform($site));
        }

        if ($this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, SiteInterface::ENTITY_TYPE)) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_site_new',
                array()
            ));
        }
        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_collection';
    }
}
