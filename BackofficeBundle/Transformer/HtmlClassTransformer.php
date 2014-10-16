<?php

namespace PHPOrchestra\BackofficeBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class HtmlClassTransformer
 */
class HtmlClassTransformer implements DataTransformerInterface
{
    /**
     * Transforms a normalized html class to a view html class
     *
     * @param string $normData
     * @return string
     */
    public function transform($normData)
    {
        return $normData;
    }

    /**
     * Transforms a view html class to a normalized html class
     *
     * @param string $viewData
     * @return string
     */
    public function reverseTransform($viewData)
    {
        return preg_replace('/["\'<>]/', '-', $viewData);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'html_class';
    }
}
