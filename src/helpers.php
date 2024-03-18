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
    function ddapi(mixed ...$data)
    {
        $exceptionData = [];
        foreach ($data as $datum) {
            $exceptionData[] = $datum;
        }
        throw new DumpAPIException($exceptionData);
    }
}
