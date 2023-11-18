<?php

use Iqbalatma\LaravelUtils\Exceptions\DumpAPIException;


/**
 * @param mixed $data
 * @return mixed
 * @throws DumpAPIException
 */
if (!function_exists('ddapi')) {
    /**
     * @throws DumpAPIException
     */
    function ddapi(mixed $data)
    {
        throw new DumpAPIException($data);
    }
}



/**
 * @param mixed $data
 * @return mixed
 * @throws DumpAPIException
 */
if (!function_exists('getNamespaceFromPath')) {
    /**
     * @throws DumpAPIException
     */
    function getNamespaceFromPath(string $path)
    {

    }
}

