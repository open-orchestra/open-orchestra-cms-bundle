<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class HtmlElementTransformer
 */
class HtmlElementTransformer implements DataTransformerInterface
{
    /**
     * Transforms a normalized html id to a view html id
     *
     * @param string $normData
     *
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
}
