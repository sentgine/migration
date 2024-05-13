<?php
if (!function_exists('dump')) {
    /**
     * Output data in a readable format.
     *
     * @param mixed $data The data to be dumped.
     * @return void
     */
    function dump(mixed $data = []): void
    {
        print_r($data);
    }
}

if (!function_exists('dumpx')) {
    /**
     * Output data in a readable format and exit the script.
     *
     * @param mixed $data The data to be dumped.
     * @return void
     */
    function dumpx(mixed $data = []): void
    {
        dump($data);
        exit;
    }
}
