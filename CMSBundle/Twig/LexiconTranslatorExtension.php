<?php

namespace PHPOrchestra\CMSBundle\Twig;

/**
 * Class LexiconTranslatorExtension
 */
class LexiconTranslatorExtension extends \Twig_Extension
{

    protected $lexic = array('symfonyTypeToSmartType' => array('choice' => 'select',
                                    'text' => 'input'));

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('lexicon_translator', array($this, 'lexiconTranslatorFilter')),
        );
    }

    /**
     * @param mixed  $key
     * @param string $fromto
     *
     * @return mixed
     */
    public function lexiconTranslatorFilter($key, $fromto = "symfonyTypeToSmartType")
    {
        if (array_key_exists($fromto, $this->lexic) && array_key_exists($key, $this->lexic[$fromto])) {
            return $this->lexic[$fromto][$key];
        } else {
            return $key;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lexicon_translator';
    }
}
