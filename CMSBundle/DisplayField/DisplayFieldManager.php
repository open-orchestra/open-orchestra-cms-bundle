<?php

namespace PHPOrchestra\CMSBundle\DisplayField;
use PHPOrchestra\CMSBundle\Exception\DisplayFieldStrategyNotFoundException;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class DisplayFieldManager
 */
class DisplayFieldManager
{
    protected $strategies = array();

    /**
     * @param DisplayFieldInterface $strategy
     */
    public function addStrategy(DisplayFieldInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param string $fieldType
     *
     * @throws DisplayFieldStrategyNotFoundException
     * @return string
     */
    public function getHtmlField($fieldType)
    {
        /** @var DisplayFieldInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($fieldType)) {
                return $strategy->getHtmlField();
            }
        }

        throw new DisplayFieldStrategyNotFoundException('No html field for field \'s type : '.$fieldType);
    }

    /**
     * @param string $fieldType
     *
     * @throws DisplayFieldStrategyNotFoundException
     * @return string
     */
    public function getHtmlEnd($fieldType)
    {
        /** @var DisplayFieldInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($fieldType)) {
                return $strategy->getHtmlEnd();
            }
        }

        throw new DisplayFieldStrategyNotFoundException('No html field for field \'s type : '.$fieldType);
    }

    /**
     * @param string $fieldType
     * @param string $class
     *
     * @throws DisplayFieldStrategyNotFoundException
     * @return string
     */
    public function setClass($fieldType, $class)
    {
        /** @var DisplayFieldInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($fieldType)) {
                return $strategy->setClass($class);
            }
        }

        throw new DisplayFieldStrategyNotFoundException('No html field for field \'s type : '.$fieldType);
    }

    /**
     * @param string $fieldType
     *
     * @throws DisplayFieldStrategyNotFoundException
     * @return string
     */
    public function getClass($fieldType)
    {
        /** @var DisplayFieldInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($fieldType)) {
                return $strategy->getClass();
            }
        }

        throw new DisplayFieldStrategyNotFoundException('No html field for field \'s type : '.$fieldType);
    }
}
