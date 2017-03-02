<?php
//require_once '../../../nct-config.php';
global $apiConfig;
$apiConfig = array(
    // True if objects should be returned by the service classes.
    // False if associative arrays should be returned (default behavior).
    'use_objects' => false,
    'application_name' => 'APPS',

    'oauth2_client_id' => CLIENT_ID,
    'oauth2_client_secret' => CLIENT_SECRET,
    'oauth2_redirect_uri' => SOCIAL_BASE_URL.'login.php?google',

    'developer_key' => '',
  
    'site_name' => SITE_URL,

    'authClass'    => 'Google_OAuth2',
    'ioClass'      => 'Google_CurlIO',
    'cacheClass'   => 'Google_FileCache',

    'basePath' => 'https://www.googleapis.com',

    'ioFileCache_directory'  =>
        (function_exists('sys_get_temp_dir') ?
            sys_get_temp_dir() . '/Google_Client' :
        '/tmp/Google_Client'),

    'services' => array(
      'oauth2' => array(
          'scope' => array(
              'https://www.googleapis.com/auth/userinfo.profile',
              'https://www.googleapis.com/auth/userinfo.email',
          )
      ),
    )
);