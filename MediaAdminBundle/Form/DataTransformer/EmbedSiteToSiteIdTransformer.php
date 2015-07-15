<?php

namespace OpenOrchestra\MediaAdminBundle\Form\DataTransformer;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EmbedSiteToSiteIdTransformer
 */
class EmbedSiteToSiteIdTransformer implements DataTransformerInterface
{
    /**
     * @param array $value
     *
     * @return int
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        $sites = array();

        if (!empty($value)) {
            foreach ($value as $associatedSite) {
                $sites[] = $associatedSite['siteId'];
            }
        }

        return $sites;
    }

    /**
     * @param SiteInterface $value
     *
     * @return string
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        $sites = array();

        foreach ($value as $siteId) {
            $sites[] = array('siteId' => (string)$siteId);
        }

        return $sites;
    }
}
