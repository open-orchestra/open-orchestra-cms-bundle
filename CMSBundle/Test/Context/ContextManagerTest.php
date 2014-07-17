<?php

/*
 * Business & Decision - Commercial License
 *
 * Copyright 2014 Business & Decision.
 *
 * All rights reserved. You CANNOT use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell this Software or any parts of this
 * Software, without the written authorization of Business & Decision.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * See LICENSE.txt file for the full LICENSE text.
 */

namespace PHPOrchestra\CMSBundle\Test\Context;

use PHPOrchestra\CMSBundle\Context\ContextManager;
use PHPOrchestra\CMSBundle\Test\Mock\SessionManager;

/**
 * Unit tests of contextManager
 *
 * @author Noël GILAIN <noel.gilain@businessdecision.com>
 */
class ContextManagerTest extends \PHPUnit_Framework_TestCase
{
    private $sessionManager = null;
    private $contextManager = null;
    
    /**
     * Tests setup
     */
    public function setUp()
    {
        $this->sessionManager = new SessionManager();
        $this->contextManager = new ContextManager($this->sessionManager);
    }



    /**
     * @param string $locale
     * 
     * @dataProvider getLocale
     */
    public function testGetCurrentLocale($locale)
    {
        $this->sessionManager->set('_locale', $locale);
        $this->assertEquals($locale, $this->contextManager->getCurrentLocale());
    }


    /**
     * @param string $locale
     * 
     * @dataProvider getLocale
     */
    public function testSetCurrentLocale($locale)
    {
        $this->contextManager->setCurrentLocale($locale);
        $this->assertEquals($locale, $this->sessionManager->get('_locale'));
    }


    public function getLocale()
    {
        return array(
            array(''),
            array('fr'),
            array(3),
            array('fakeKey' => 'fakeValue')
        );
    }
}
