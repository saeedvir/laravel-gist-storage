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
    */
    'gist_id' => env('GIST_ID'),

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
