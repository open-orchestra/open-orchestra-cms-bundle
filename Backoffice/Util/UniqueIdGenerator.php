<?php

namespace OpenOrchestra\Backoffice\Util;

/**
 * Class UniqueIdGenerator
 */
class UniqueIdGenerator
{
    const SEPARATOR = 'oo';

    /**
     * @return string
     */
    public function generateUniqueId()
    {
        return str_replace('.', self::SEPARATOR, uniqid("", true));
    }
}