<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repository namespace
    |--------------------------------------------------------------------------
    |
    | The namespace for the repository classes.
    |
    */
    'repository_namespace' => 'App\Repositories',

    /*
    |--------------------------------------------------------------------------
    | Repository path
    |--------------------------------------------------------------------------
    |
    | The path to the repository folder.
    |
    */
    'repository_path' => 'app'.DIRECTORY_SEPARATOR.'Repositories',

    /*
    |--------------------------------------------------------------------------
    | Criteria namespace
    |--------------------------------------------------------------------------
    |
    | The namespace for the criteria classes.
    |
    */
    'criteria_namespace' => 'App\Repositories\Criteria',

    /*
    |--------------------------------------------------------------------------
    | Criteria path
    |--------------------------------------------------------------------------
    |
    | The path to the criteria folder.
    |
    */
    'criteria_path' => 'app'.DIRECTORY_SEPARATOR.'Repositories'.DIRECTORY_SEPARATOR.'Criteria',

    /*
    |--------------------------------------------------------------------------
    | Model namespace
    |--------------------------------------------------------------------------
    |
    | The model namespace.
    |
    */
    'model_namespace' => 'App',

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
    'cache_use_tags'  => env('REPOSITORY_CACHE_TAGS', false),

];
