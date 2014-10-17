<?php

namespace PHPOrchestra\BackofficeBundle\Test\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ContextManagerClassTest
 */
class ContextManagerClassTest extends KernelTestCase
{
    /**
     * @param string $classExpected
     * @param string $env
     * @param bool   $debug
     *
     * @dataProvider provideClassAndOptions
     */
    public function testDifferentEnv($classExpected, $env, $debug)
    {
        $kernel = static::createKernel(array('environment' => $env ,'debug' => $debug));
        $kernel->boot();
        $this->assertInstanceOf(
            $classExpected,
            $kernel->getContainer()->get('php_orchestra_backoffice.context_manager')
        );
    }

    /**
     * @return array
     */
    public function provideClassAndOptions()
    {
        return array(
            array('PHPOrchestra\Backoffice\Context\TestContextManager', 'test', true),
        );
    }
}
