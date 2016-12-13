<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class SiteCollectionTransformer
 */
class SiteCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $groupCollection
     * @param array      $nbrGroupsUsers
     *
     * @return FacadeInterface
     */
    public function transform($groupCollection, array $nbrGroupsUsers = array())
    {
        $facade = $this->newFacade();

        foreach ($groupCollection as $group) {
            $facade->addGroup($this->getTransformer('group')->transform($group, (array_key_exists($group->getId(), $nbrGroupsUsers)) ? $nbrGroupsUsers[$group->getId()] : 0));
        }

        return $facade;
    }


    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return SiteInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $sites = array();
        $sitesFacade = $facade->getSites();
        foreach ($sitesFacade as $siteFacade) {
            $site = $this->getTransformer('site')->reverseTransform($siteFacade);
            if (null !== $site) {
                $sites[] = $site;
            }
        }

        return $sites;
    }

    /**
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, $source = NULL)
    {
        $groups = array();
        $groupsFacade = $facade->getGroups();
        foreach ($groupsFacade as $groupFacade) {
            $group = $this->getTransformer('group')->reverseTransform($groupFacade);
            if (null !== $group) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_collection';
    }
}
