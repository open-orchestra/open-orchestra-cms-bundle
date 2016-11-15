<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;

/**
 * Class SiteAliasTransformer
 */
class SiteAliasTransformer extends AbstractTransformer
{
    /**
     * @param SiteAliasInterface $siteAlias
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($siteAlias)
    {
        if (!$siteAlias instanceof SiteAliasInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->domain = $siteAlias->getDomain();
        $facade->language = $siteAlias->getLanguage();
        $facade->scheme = $siteAlias->getScheme();
        $facade->prefix= $siteAlias->getPrefix();

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site_alias';
    }
}
