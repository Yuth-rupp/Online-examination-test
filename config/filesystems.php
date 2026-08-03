<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        // NOTE: Railway's container filesystem is ephemeral — anything written to
        // local disk is wiped on every restart/redeploy (crash, redeploy, scale
        // event, etc). Anything saved to the 'local' or 'public' disks (profile
        // photos, exam screenshots, backups) would be lost. Set
        // FILESYSTEM_LOCAL_DRIVER=s3 and FILESYSTEM_PUBLIC_DRIVER=s3 in Railway's
        // service variables to route these disks to a Railway Bucket (or any
        // S3-compatible storage) instead. Local dev is unaffected since those
        // env vars default to 'local'.
        'local' => [
            'driver' => env('FILESYSTEM_LOCAL_DRIVER', 'local'),
            'root' => env('FILESYSTEM_LOCAL_DRIVER', 'local') === 's3'
                ? 'private'
                : storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
            // S3/R2 keys below are only used when driver is 's3'
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
        ],

        'public' => [
            'driver' => env('FILESYSTEM_PUBLIC_DRIVER', 'local'),
            'root' => env('FILESYSTEM_PUBLIC_DRIVER', 'local') === 's3'
                ? 'public'
                : storage_path('app/public'),
            'url' => env('FILESYSTEM_PUBLIC_DRIVER', 'local') === 's3'
                ? env('AWS_URL')
                : rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            // R2 does NOT support per-object ACLs like AWS S3 does — sending a
            // visibility/ACL header on upload causes R2 to reject the request.
            // Since 'throw' is false below, that rejection was being swallowed
            // silently: store() returned false, an empty value got saved to
            // avatar_url/profile_image, and the image appeared to upload fine
            // (client-side preview) but reverted to default on next login.
            // Public access on R2 is granted at the BUCKET level instead
            // (Settings -> Public Access -> R2.dev URL), so no ACL is needed
            // when driver is 's3'.
            'visibility' => env('FILESYSTEM_PUBLIC_DRIVER', 'local') === 's3' ? null : 'public',
            // Was 'throw' => false, which silently swallowed upload failures —
            // store() would just return false with no exception and no log
            // entry, so a failed R2 upload was indistinguishable from a
            // successful one until the user logged back in and found the
            // image gone. Now failures throw, and the controllers below
            // catch + log them so the real error is visible.
            'throw' => true,
            'report' => false,
            // S3/R2 keys below are only used when driver is 's3'
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // Dedicated disk for Super Admin database backup snapshots.
        // Same S3-toggle pattern as 'local'/'public' above: defaults to the
        // container's local disk (fine for local dev), but set
        // FILESYSTEM_BACKUPS_DRIVER=s3 in Railway to route backups to the
        // same Railway Bucket / S3-compatible storage — otherwise every
        // snapshot is wiped the next time the container restarts or
        // redeploys, and delete/restore will fail with "not found" because
        // the file genuinely no longer exists.
        'backups' => [
            'driver' => env('FILESYSTEM_BACKUPS_DRIVER', 'local'),
            'root' => env('FILESYSTEM_BACKUPS_DRIVER', 'local') === 's3'
                ? 'backups'
                : storage_path('app/backups'),
            // Unlike avatars, a silently-failed backup write is unacceptable —
            // we need to know immediately if a snapshot failed to persist.
            'throw' => true,
            'report' => false,
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];