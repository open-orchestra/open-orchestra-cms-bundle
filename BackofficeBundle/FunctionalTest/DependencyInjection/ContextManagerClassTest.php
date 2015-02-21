<?php

namespace OpenOrchestra\BackofficeBundle\FunctionalTest\DependencyInjection;

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
            $kernel->getContainer()->get('open_orchestra_backoffice.context_manager')
        );
    }

    /**
     * @return array
     */
    public function provideClassAndOptions()
    {
        return array(
            array('OpenOrchestra\Backoffice\Context\TestContextManager', 'test', true),
        );
    }
}
