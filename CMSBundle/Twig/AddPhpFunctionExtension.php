<?php

namespace PHPOrchestra\CMSBundle\Twig;

use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;

/**
 * Class AddPhpFunctionExtension
 */
class AddPhpFunctionExtension extends \Twig_Extension
{
    protected $templating;

    /**
     * @param TimedTwigEngine $templating
     */
    public function __construct(TimedTwigEngine $templating)
    {
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('file_exists', array($this, 'fileExists')),
            new \Twig_SimpleFunction('twig_exists', array($this, 'twigExists')),
            new \Twig_SimpleFunction('e_crc32', array($this, 'eCrc32')),
        );
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function fileExists($file)
    {
        return file_exists($file);
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function twigExists($file)
    {
        return $this->templating->exists($file);
    }

    /**
     * @param string $value
     *
     * @return int
     */
    public function eCrc32($value)
    {
        return crc32($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'php_function';
    }
}
