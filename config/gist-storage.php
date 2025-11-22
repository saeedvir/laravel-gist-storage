<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GitHub Personal Access Token
    |--------------------------------------------------------------------------
    |
    | Your GitHub Personal Access Token with "gist" scope enabled.
    | Create one at: https://github.com/settings/tokens
    |
    */
    'token' => env('GIST_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Gist ID
    |--------------------------------------------------------------------------
    |
    | The ID of the Gist where files will be stored.
    | You can find this in the URL of your gist.
    | Example: https://gist.github.com/username/a1b2c3d4e5f6g7h8i9j0
    | The gist_id would be: a1b2c3d4e5f6g7h8i9j0
    |
    | Leave this null if you want to auto-create a new gist (requires auto_create = true)
    |
    */
    'gist_id' => env('GIST_ID'),

    /*
    |--------------------------------------------------------------------------
    | Auto Create Gist
    |--------------------------------------------------------------------------
    |
    | If true, a new gist will be automatically created when you first write a file.
    | The gist ID will be stored in memory for subsequent operations.
    | If false, you must provide a gist_id.
    |
    */
    'auto_create' => env('GIST_AUTO_CREATE', false),

    /*
    |--------------------------------------------------------------------------
    | Public Gist
    |--------------------------------------------------------------------------
    |
    | Whether files uploaded should be public or secret.
    | Note: This applies when creating new gists, not updating existing ones.
    |
    */
    'public' => env('GIST_PUBLIC', false),

    /*
    |--------------------------------------------------------------------------
    | Gist Description
    |--------------------------------------------------------------------------
    |
    | Default description for the gist.
    |
    */
    'description' => env('GIST_DESCRIPTION', 'Files uploaded via Laravel'),
];
