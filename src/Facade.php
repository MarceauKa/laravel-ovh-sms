<?php

namespace Akibatech\Ovhsms;

/**
 * Class Facade
 *
 * @package Akibatech\Ovhsms
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * @param   void
     * @return  string
     */
    protected static function getFacadeAccessor()
    {
        return 'ovhsms';
    }
}