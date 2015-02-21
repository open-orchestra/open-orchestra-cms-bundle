<?php

namespace OpenOrchestra\BackofficeBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class HtmlIdTransformer
 */
class HtmlIdTransformer implements DataTransformerInterface
{
    /**
     * Transforms a normalized html id to a view html id
     *
     * @param string $normData
     * @return string
     */
    public function transform($normData)
    {
        return $normData;
    }

    /**
     * Transforms a view html id to a normalized html id
     *
     * @param string $viewData
     * @return string
     */
    public function reverseTransform($viewData)
    {
        return preg_replace('/["\'<> ]/', '-', $viewData);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'html_id';
    }
}
