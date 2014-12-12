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
        $this->renderFile('backofficeDisplayBlock/Strategy.php.twig', $this->rootDir .'/'. $backofficeDisplayDir . '/' . $className . 'Strategy.php', $parameters);
        $this->renderFile('backofficeDisplayBlock/show.html.twig.twig', $this->rootDir .'/'. $backofficeDisplayDir . '/../../Resources/views/Block' . '/' . $className . '/show.html.twig', $parameters);

        $parameters['namespace'] = $backofficeIconNamespace;
        $this->renderFile('displayIcon/Strategy.php.twig', $this->rootDir .'/'. $backofficeIconDir . '/' . $className . 'Strategy.php', $parameters);
        $this->renderFile('displayIcon/showIcon.html.twig.twig', $this->rootDir .'/'. $backofficeIconDir . '/../../Resources/views/Block' . '/' . $className . '/showIcon.html.twig', $parameters);

        $parameters['namespace'] = $generatorFormNamespace;
        $this->renderFile('generateForm/Strategy.php.twig', $this->rootDir .'/'. $generatorFormDir . '/' . $className . 'Strategy.php', $parameters);

        $parameters['namespace'] = $frontDisplayNamespace;
        $this->renderFile('frontDisplayBlock/Strategy.php.twig', $this->rootDir .'/'. $frontDisplayDir . '/' . $className . 'Strategy.php', $parameters);
        $this->renderFile('displayIcon/showIcon.html.twig.twig', $this->rootDir .'/'. $frontDisplayDir . '/../../Resources/views/Block' . '/' . $className . '/show.html.twig', $parameters);
    }
}
