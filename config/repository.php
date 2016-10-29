<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repository Cache Enabled
    |--------------------------------------------------------------------------
    |
    | Repository Cache Enabled or not.
    |
    */
    'cache_enabled'   => env('REPOSITORY_CACHE', true),

    /*
    |--------------------------------------------------------------------------
    | Repository Cache TTL
    |--------------------------------------------------------------------------
    |
    | The TTL value of Repository Cache.
    |
    */
    'cache_ttl'       => env('REPOSITORY_CACHE_TTL', 30),

    /*
    |--------------------------------------------------------------------------
    | Repository Cache Tags Enabled
    |--------------------------------------------------------------------------
    |
    | Repository Cache Tags Enabled or not.
    |
    */
    'cache_use_tags'  => env('REPOSITORY_CACHE_TAGS', true),

];
