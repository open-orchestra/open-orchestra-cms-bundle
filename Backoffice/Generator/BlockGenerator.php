<?php

namespace PHPOrchestra\Backoffice\Generator;

use Doctrine\Common\Util\Inflector;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

/**
 * Class BlockGenerator
 */
class BlockGenerator extends Generator
{
    protected $rootDir;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $blockName
     * @param string $generatorFormDir
     * @param string $generatorFormNamespace
     * @param string $frontDisplayDir
     * @param string $frontDisplayNamespace
     * @param string $backofficeIconDir
     * @param string $backofficeIconNamespace
     * @param string $backofficeDisplayDir
     * @param string $backofficeDisplayNamespace
     */
    public function generate(
        $blockName,
        $generatorFormDir,
        $generatorFormNamespace,
        $frontDisplayDir,
        $frontDisplayNamespace,
        $backofficeIconDir,
        $backofficeIconNamespace,
        $backofficeDisplayDir,
        $backofficeDisplayNamespace
    )
    {
        $className = Inflector::classify($blockName);
        $strategyName = Inflector::tableize($blockName);

        $parameters = array(
            'className' => $className,
            'strategyName' => $strategyName,
        );

        $parameters['namespace'] = $backofficeDisplayNamespace;
        $target = $this->rootDir . '/' . $backofficeDisplayDir . '/' . $className . 'Strategy.php';
        if (!file_exists($target)) {
            $this->renderFile('backofficeDisplayBlock/Strategy.php.twig', $target, $parameters);
            $this->renderFile('backofficeDisplayBlock/show.html.twig.twig', $this->rootDir .'/'. $backofficeDisplayDir . '/../../Resources/views/Block' . '/' . $className . '/show.html.twig', $parameters);
        }

        $parameters['namespace'] = $backofficeIconNamespace;
        $target = $this->rootDir . '/' . $backofficeIconDir . '/' . $className . 'Strategy.php';
        if (!file_exists($target)) {
            $this->renderFile('displayIcon/Strategy.php.twig', $target, $parameters);
            $this->renderFile('displayIcon/showIcon.html.twig.twig', $this->rootDir .'/'. $backofficeIconDir . '/../../Resources/views/Block' . '/' . $className . '/showIcon.html.twig', $parameters);
        }

        $parameters['namespace'] = $generatorFormNamespace;
        $target = $this->rootDir . '/' . $generatorFormDir . '/' . $className . 'Strategy.php';
        if (!file_exists($target)) {
            $this->renderFile('generateForm/Strategy.php.twig', $target, $parameters);
        }

        $parameters['namespace'] = $frontDisplayNamespace;
        $target = $this->rootDir . '/' . $frontDisplayDir . '/' . $className . 'Strategy.php';
        if (!file_exists($target)) {
            $this->renderFile('frontDisplayBlock/Strategy.php.twig', $target, $parameters);
            $this->renderFile('displayIcon/showIcon.html.twig.twig', $this->rootDir . '/' . $frontDisplayDir . '/../../Resources/views/Block' . '/' . $className . '/show.html.twig', $parameters);
        }
    }
}
