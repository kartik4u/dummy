<?php return array (
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
      ),
    ),
    'failed' => 
    array (
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'smtp.sendgrid.net',
    'port' => '2525',
    'from' => 
    array (
      'address' => 'hello@example.com',
      'name' => 'Example',
    ),
    'encryption' => 'tls',
    'username' => 'covercare',
    'password' => 'SghWGD451',
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/var/www/html/edseen/resources/views/vendor/mail',
      ),
    ),
  ),
  'constants' => 
  array (
    'SITE_NAME' => 'Racestake',
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'token',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
      ),
    ),
  ),
  'l5-swagger' => 
  array (
    'api' => 
    array (
      'title' => 'L5 Swagger UI',
    ),
    'routes' => 
    array (
      'api' => 'api/documentation',
      'docs' => 'docs',
      'oauth2_callback' => 'api/oauth2-callback',
      'middleware' => 
      array (
        'api' => 
        array (
        ),
        'asset' => 
        array (
        ),
        'docs' => 
        array (
        ),
        'oauth2_callback' => 
        array (
        ),
      ),
    ),
    'paths' => 
    array (
      'docs' => '/var/www/html/edseen/storage/api-docs',
      'docs_json' => 'api-docs.json',
      'annotations' => '/var/www/html/edseen/app',
      'views' => '/var/www/html/edseen/resources/views/vendor/l5-swagger',
      'base' => NULL,
      'excludes' => 
      array (
      ),
    ),
    'security' => 
    array (
    ),
    'generate_always' => true,
    'swagger_version' => '2.0',
    'proxy' => false,
    'additional_config_url' => NULL,
    'operations_sort' => NULL,
    'validator_url' => NULL,
    'constants' => 
    array (
      'L5_SWAGGER_CONST_HOST' => 'http://my-default-host.com',
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/var/www/html/edseen/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
    'prefix' => 'laravel',
  ),
  'error' => 
  array (
    'norecord_found' => 
    array (
      'status' => 400,
      'description' => 'No record found.',
    ),
    'incorrect_id' => 
    array (
      'status' => 400,
      'description' => 'Incorrect id entered.',
    ),
    'privacy_terms_error' => 
    array (
      'status' => 400,
      'description' => 'Please agree to terms & conditions and privacy policy first.',
    ),
    'email_already_exists' => 
    array (
      'status' => 400,
      'description' => 'Email already exists. Please try another one.',
    ),
    'failed_to_save_details' => 
    array (
      'status' => 400,
      'description' => 'Failes to save details.',
    ),
    'invalid_email' => 
    array (
      'status' => 400,
      'description' => 'Please enter valid email id.',
    ),
    'forgot_token_expired' => 
    array (
      'status' => 400,
      'description' => 'Token expired.',
    ),
    'valid_credentials' => 
    array (
      'status' => 400,
      'description' => 'Please enter valid credentials.',
    ),
    'account_not_activate' => 
    array (
      'status' => 400,
      'description' => 'Your account is inactive. Please activate your account.',
    ),
    'wrong_credentials' => 
    array (
      'status' => 400,
      'description' => 'Invalid credentials.',
    ),
    'failed_to_create_token' => 
    array (
      'status' => 400,
      'description' => 'Failed to create token.',
    ),
    'empty_token' => 
    array (
      'status' => 400,
      'description' => 'Empty token.',
    ),
    'failed_to_update_token' => 
    array (
      'status' => 400,
      'description' => 'Failed to update token.',
    ),
    'logout_failed' => 
    array (
      'status' => 400,
      'description' => 'Failed to logged out.',
    ),
    'invalid_request' => 
    array (
      'status' => 400,
      'description' => 'Invalid request.',
    ),
    'invalid_user' => 
    array (
      'status' => 400,
      'description' => 'Invalid user.',
    ),
    'invalid_link' => 
    array (
      'status' => 400,
      'description' => 'Invalid link.',
    ),
    'failed_to_send_email' => 
    array (
      'status' => 400,
      'description' => 'Failed to send email.',
    ),
    'failed_to_update_password' => 
    array (
      'status' => 400,
      'description' => 'Failed to update password.',
    ),
    'old_password_incorrect' => 
    array (
      'status' => 400,
      'description' => 'Please enter correct current password.',
    ),
    'link_expired' => 
    array (
      'status' => 400,
      'description' => 'Link has been expired or invalid link.',
    ),
    'fail_to_verify' => 
    array (
      'status' => 400,
      'description' => 'Failed to verify user. Please try again.',
    ),
    'not_verified_by_admin' => 
    array (
      'status' => 400,
      'description' => 'Your account is not verified by admin yet.Please wait for approval.',
    ),
    'failed_password_reset' => 
    array (
      'status' => 400,
      'description' => 'Failed to rest password.',
    ),
    'failed_to_update' => 
    array (
      'status' => 400,
      'description' => 'Failed to update data. Please try again',
    ),
    'deactivated_by_admin' => 
    array (
      'status' => 400,
      'description' => 'Your account has been deactivated by admin. Please contact admin for queries.',
    ),
    'course_name_aleready_taken' => 
    array (
      'status' => 400,
      'description' => 'The course name has already been taken.',
    ),
    'failed_to_delete_user' => 
    array (
      'status' => 400,
      'description' => 'Failed to delete user.',
    ),
    'terms_privacy_error' => 
    array (
      'status' => 400,
      'description' => 'Failed to accept terms condition & privacy policy.',
    ),
    'error_alerady_exists_user_email' => 
    array (
      'status' => 400,
      'description' => 'Email has already been registered.',
    ),
    'no_record_found' => 
    array (
      'status' => 400,
      'description' => 'No record found.',
    ),
    'university_not_activate' => 
    array (
      'status' => 400,
      'description' => 'University not activated, Please try again.',
    ),
    'university_not_deactivate' => 
    array (
      'status' => 400,
      'description' => 'University not deactivated, Please try again.',
    ),
    'university_not_approved' => 
    array (
      'status' => 400,
      'description' => 'University not approve, Please try again.',
    ),
    'university_not_rejected' => 
    array (
      'status' => 400,
      'description' => 'University not rejected, Please try again.',
    ),
  ),
  'mangopay' => 
  array (
  ),
  'variable' => 
  array (
    'ADMIN_EMAIL' => 'prabhat.thakur@ignivasolutions.com',
    'MAIL_FROM_NAME' => 'Edseen',
    'ADMIN_URL' => 'http://admin.unseen.com/',
    'SERVER_URL' => 'http://server.unseen.com/',
    'FRONTEND_URL' => 'http://frontend.unseen.com/',
    'page_per_record' => 10,
    'excel_limit_per_file' => 200,
    'REGISTER_EMAIL_SUBJECT' => 'Registration : Verify Account',
    'FORGOT_EMAIL_SUBJECT' => 'Edseen: Reset Your Password',
    'CONTACT_EMAIL_SUBJECT' => 'Edseen: Contact Us',
    'EMAIL_VERIFICATION' => 'Edseen: Email Verification',
    'ACCOUNT_DELETE_PERMANENT' => 'Edseen: Account Delete',
  ),
  'debugbar' => 
  array (
    'enabled' => NULL,
    'storage' => 
    array (
      'enabled' => true,
      'driver' => 'file',
      'path' => '/var/www/html/edseen/storage/debugbar',
      'connection' => NULL,
      'provider' => '',
    ),
    'include_vendors' => true,
    'capture_ajax' => true,
    'add_ajax_timing' => false,
    'error_handler' => false,
    'clockwork' => false,
    'collectors' => 
    array (
      'phpinfo' => true,
      'messages' => true,
      'time' => true,
      'memory' => true,
      'exceptions' => true,
      'log' => true,
      'db' => true,
      'views' => true,
      'route' => true,
      'auth' => true,
      'gate' => true,
      'session' => true,
      'symfony_request' => true,
      'mail' => true,
      'laravel' => false,
      'events' => false,
      'default_request' => false,
      'logs' => false,
      'files' => false,
      'config' => false,
    ),
    'options' => 
    array (
      'auth' => 
      array (
        'show_name' => true,
      ),
      'db' => 
      array (
        'with_params' => true,
        'backtrace' => true,
        'timeline' => false,
        'explain' => 
        array (
          'enabled' => false,
          'types' => 
          array (
            0 => 'SELECT',
          ),
        ),
        'hints' => true,
      ),
      'mail' => 
      array (
        'full_log' => false,
      ),
      'views' => 
      array (
        'data' => false,
      ),
      'route' => 
      array (
        'label' => true,
      ),
      'logs' => 
      array (
        'file' => NULL,
      ),
    ),
    'inject' => true,
    'route_prefix' => '_debugbar',
    'route_domain' => NULL,
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => 'edseen',
        'prefix' => '',
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => '192.168.0.239',
        'port' => '3306',
        'database' => 'edseen',
        'username' => 'edseen',
        'password' => 'edseas@13hdfhK',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => NULL,
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => '192.168.0.239',
        'port' => '3306',
        'database' => 'edseen',
        'username' => 'edseen',
        'password' => 'edseas@13hdfhK',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'host' => '192.168.0.239',
        'port' => '3306',
        'database' => 'edseen',
        'username' => 'edseen',
        'password' => 'edseas@13hdfhK',
        'charset' => 'utf8',
        'prefix' => '',
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'default' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => 0,
      ),
    ),
  ),
  'jwt' => 
  array (
    'secret' => 'JDBMPRmTKL5I5S4q7d4lkiy8ndFXnS80',
    'ttl' => 1440,
    'refresh_ttl' => 20160,
    'algo' => 'HS256',
    'user' => 'App\\User',
    'identifier' => 'id',
    'required_claims' => 
    array (
      0 => 'iss',
      1 => 'iat',
      2 => 'exp',
      3 => 'nbf',
      4 => 'sub',
      5 => 'jti',
    ),
    'blacklist_enabled' => true,
    'providers' => 
    array (
      'user' => 'Tymon\\JWTAuth\\Providers\\User\\EloquentUserAdapter',
      'jwt' => 'Tymon\\JWTAuth\\Providers\\JWT\\NamshiAdapter',
      'auth' => 'Tymon\\JWTAuth\\Providers\\Auth\\IlluminateAuthAdapter',
      'storage' => 'Tymon\\JWTAuth\\Providers\\Storage\\IlluminateCacheAdapter',
    ),
  ),
  'app' => 
  array (
    'name' => 'Edseen',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://server.edseen.com',
    'timezone' => 'Asia/Kolkata',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => 'base64:NXvP7UtHe2yWtPx1A/iSRZDlT4FUDH3/0FdWUbbIoHI=',
    'cipher' => 'AES-256-CBC',
    'log' => 'single',
    'log_level' => 'debug',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Folklore\\Image\\ImageServiceProvider',
      23 => 'Laravel\\Tinker\\TinkerServiceProvider',
      24 => 'App\\Providers\\AppServiceProvider',
      25 => 'App\\Providers\\AuthServiceProvider',
      26 => 'App\\Providers\\EventServiceProvider',
      27 => 'App\\Providers\\RouteServiceProvider',
      28 => 'Tymon\\JWTAuth\\Providers\\JWTAuthServiceProvider',
      29 => 'L5Swagger\\L5SwaggerServiceProvider',
      30 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      31 => 'Cviebrock\\LaravelMangopay\\ServiceProvider',
      32 => 'Laravel\\Cashier\\CashierServiceProvider',
      33 => 'SimpleSoftwareIO\\QrCode\\QrCodeServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'JWTAuth' => 'Tymon\\JWTAuth\\Facades\\JWTAuth',
      'Image' => 'Folklore\\Image\\Facades\\Image',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'QrCode' => 'SimpleSoftwareIO\\QrCode\\Facades\\QrCode',
    ),
  ),
  'paypal' => 
  array (
    'client_id' => 'AcJ0HvG0fMEJblx1wl12_LPAFK6XrVLqGuRxQp9pAGh-VlWJFxxFfyGzHDDMuAtALA9PuXy_OOKp8NT_',
    'secret' => 'EAQ3QlIo2oGAHGeOB8YCCRtSfg275T-ChqT8zWhhcpEooFvPmObWWfbTk9yGUVWhl3WzkUb2acr5swp4',
    'settings' => 
    array (
      'mode' => 'sandbox',
      'http.ConnectionTimeOut' => 1000,
      'log.LogEnabled' => true,
      'log.FileName' => '/var/www/html/edseen/storage/logs/paypal.log',
      'log.LogLevel' => 'FINE',
    ),
  ),
  'tinker' => 
  array (
    'dont_alias' => 
    array (
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/var/www/html/edseen/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => false,
    'http_only' => true,
  ),
  'image' => 
  array (
    'driver' => 'gd',
    'memory_limit' => '128M',
    'src_dirs' => 
    array (
      0 => '/var/www/html/edseen/public',
    ),
    'host' => '',
    'pattern' => '^(.*){parameters}\\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$',
    'url_parameter' => '-image({options})',
    'url_parameter_separator' => '-',
    'serve' => true,
    'serve_domain' => NULL,
    'serve_route' => '{image_pattern}',
    'serve_custom_filters_only' => false,
    'serve_expires' => 2678400,
    'write_image' => false,
    'write_path' => NULL,
    'proxy' => false,
    'proxy_expires' => NULL,
    'proxy_route' => '{image_proxy_pattern}',
    'proxy_route_pattern' => NULL,
    'proxy_route_domain' => NULL,
    'proxy_filesystem' => 'cloud',
    'proxy_write_image' => true,
    'proxy_cache' => true,
    'proxy_cache_filesystem' => NULL,
    'proxy_cache_expiration' => 1440,
    'proxy_tmp_path' => '/tmp',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/var/www/html/edseen/resources/views',
    ),
    'compiled' => '/var/www/html/edseen/storage/framework/views',
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'success' => 
  array (
    'record_found' => 
    array (
      'status' => 200,
      'description' => 'Record found successfully.',
    ),
    'add_address' => 
    array (
      'status' => 200,
      'description' => 'Address added successfully.',
    ),
    'edit_address' => 
    array (
      'status' => 200,
      'description' => 'Address updated successfully.',
    ),
    'delete_address' => 
    array (
      'status' => 200,
      'description' => 'Address deleted successfully.',
    ),
    'add_courses' => 
    array (
      'status' => 200,
      'description' => 'Course added successfully.',
    ),
    'edit_courses' => 
    array (
      'status' => 200,
      'description' => 'Course updated successfully.',
    ),
    'invalid_email_user_deleted' => 
    array (
      'status' => 200,
      'description' => 'User deleted successfully but failed to send email to user.',
    ),
    'success_user_created' => 
    array (
      'status' => 200,
      'description' => 'Successfully signed up. Please check your email for verification',
    ),
    'success_forgot_password' => 
    array (
      'status' => 200,
      'description' => 'An email has been sent to you. Please check your mail.',
    ),
    'success_password_reset' => 
    array (
      'status' => 200,
      'description' => 'Password updated successfully.',
    ),
    'success_login' => 
    array (
      'status' => 200,
      'description' => 'Login successful.',
    ),
    'success_logout' => 
    array (
      'status' => 200,
      'description' => 'Logout successful.',
    ),
    'success_contact_mail' => 
    array (
      'status' => 200,
      'description' => 'Thank you for your message. A member of our team will get back to you as soon as possible.',
    ),
    'success_password_update' => 
    array (
      'status' => 200,
      'description' => 'Password updated successfully.',
    ),
    'success_email_verified' => 
    array (
      'status' => 200,
      'description' => 'Congratulations!! Account successfully verified. Kindly wait for the approval of admin.',
    ),
    'success_email_verified_student' => 
    array (
      'status' => 200,
      'description' => 'Congratulations!! Account successfully verified.',
    ),
    'success_record_found' => 
    array (
      'status' => 200,
      'description' => 'Record found',
    ),
    'success_page_updated' => 
    array (
      'status' => 200,
      'description' => 'Page updated successfully.',
    ),
    'user_deleted_successfully' => 
    array (
      'status' => 200,
      'description' => 'Account deleted successfully.',
    ),
    'terms_privacy_accept_success' => 
    array (
      'status' => 200,
      'description' => 'Terms & Conditions/Privacy Policy accepted successfully.',
    ),
    'success_data' => 
    array (
      'status' => 200,
      'description' => 'Data fetched successfully',
    ),
    'no_record' => 
    array (
      'status' => 200,
      'description' => 'No record found.',
    ),
    'success_update_university' => 
    array (
      'status' => 200,
      'description' => 'University updated successfully',
    ),
    'success_email_changed' => 
    array (
      'status' => 201,
      'description' => 'Profile updated successfully. Please check your email for verification.',
    ),
    'success_profile_updated' => 
    array (
      'status' => 200,
      'description' => 'Profile has been updated successfully.',
    ),
    'success_image_updated' => 
    array (
      'status' => 200,
      'description' => 'Image saved successfully.',
    ),
    'no_record_found' => 
    array (
      'status' => 200,
      'description' => 'No record found.',
    ),
    'delete_course' => 
    array (
      'status' => 200,
      'description' => 'Course removed successfully.',
    ),
    'success_page_show_reset_password' => 
    array (
      'status' => 700,
      'description' => 'Show reset password page.',
    ),
    'success_data_fetcheted_successfully' => 
    array (
      'status' => 200,
      'description' => 'record found succesfully.',
    ),
    'success_course_favorite' => 
    array (
      'status' => 200,
      'description' => 'Course is marked as favourite.',
    ),
    'success_course_unfavorite' => 
    array (
      'status' => 200,
      'description' => 'Course is marked as unfavourite.',
    ),
    'university_activate' => 
    array (
      'status' => 200,
      'description' => 'University has been activated successfully.',
    ),
    'university_deactivate' => 
    array (
      'status' => 200,
      'description' => 'University has been deactivated successfully.',
    ),
    'university_approved' => 
    array (
      'status' => 200,
      'description' => 'University has been approved successfully.',
    ),
    'university_rejected' => 
    array (
      'status' => 200,
      'description' => 'University has been rejected successfully.',
    ),
    'save_university_data' => 
    array (
      'status' => 200,
      'description' => 'University profile has been saved successfully.',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => NULL,
    ),
    'stripe' => 
    array (
      'model' => 'App\\User',
      'key' => NULL,
      'secret' => NULL,
    ),
    'mangopay' => 
    array (
      'env' => 'sandbox',
      'key' => 'igniva44',
      'secret' => 'nn6vMU11art1KoLgwFGhQBxDmE9mK0GefLyxgso1ec639RCqHV',
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/html/edseen/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/html/edseen/storage/app/public',
        'url' => 'http://server.edseen.com/storage',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => NULL,
        'secret' => NULL,
        'region' => NULL,
        'bucket' => NULL,
      ),
    ),
  ),
  'excel' => 
  array (
    'cache' => 
    array (
      'enable' => true,
      'driver' => 'memory',
      'settings' => 
      array (
        'memoryCacheSize' => '32MB',
        'cacheTime' => 600,
      ),
      'memcache' => 
      array (
        'host' => 'localhost',
        'port' => 11211,
      ),
      'dir' => '/var/www/html/edseen/storage/cache',
    ),
    'properties' => 
    array (
      'creator' => 'Maatwebsite',
      'lastModifiedBy' => 'Maatwebsite',
      'title' => 'Spreadsheet',
      'description' => 'Default spreadsheet export',
      'subject' => 'Spreadsheet export',
      'keywords' => 'maatwebsite, excel, export',
      'category' => 'Excel',
      'manager' => 'Maatwebsite',
      'company' => 'Maatwebsite',
    ),
    'sheets' => 
    array (
      'pageSetup' => 
      array (
        'orientation' => 'portrait',
        'paperSize' => '9',
        'scale' => '100',
        'fitToPage' => false,
        'fitToHeight' => true,
        'fitToWidth' => true,
        'columnsToRepeatAtLeft' => 
        array (
          0 => '',
          1 => '',
        ),
        'rowsToRepeatAtTop' => 
        array (
          0 => 0,
          1 => 0,
        ),
        'horizontalCentered' => false,
        'verticalCentered' => false,
        'printArea' => NULL,
        'firstPageNumber' => NULL,
      ),
    ),
    'creator' => 'Maatwebsite',
    'csv' => 
    array (
      'delimiter' => ',',
      'enclosure' => '"',
      'line_ending' => '
',
      'use_bom' => false,
    ),
    'export' => 
    array (
      'autosize' => true,
      'autosize-method' => 'approx',
      'generate_heading_by_indices' => true,
      'merged_cell_alignment' => 'left',
      'calculate' => false,
      'includeCharts' => false,
      'sheets' => 
      array (
        'page_margin' => false,
        'nullValue' => NULL,
        'startCell' => 'A1',
        'strictNullComparison' => false,
      ),
      'store' => 
      array (
        'path' => '/var/www/html/edseen/storage/exports',
        'returnInfo' => false,
      ),
      'pdf' => 
      array (
        'driver' => 'DomPDF',
        'drivers' => 
        array (
          'DomPDF' => 
          array (
            'path' => '/var/www/html/edseen/vendor/dompdf/dompdf/',
          ),
          'tcPDF' => 
          array (
            'path' => '/var/www/html/edseen/vendor/tecnick.com/tcpdf/',
          ),
          'mPDF' => 
          array (
            'path' => '/var/www/html/edseen/vendor/mpdf/mpdf/',
          ),
        ),
      ),
    ),
    'filters' => 
    array (
      'registered' => 
      array (
        'chunk' => 'Maatwebsite\\Excel\\Filters\\ChunkReadFilter',
      ),
      'enabled' => 
      array (
      ),
    ),
    'import' => 
    array (
      'heading' => 'slugged',
      'startRow' => 1,
      'separator' => '_',
      'slug_whitelist' => '._',
      'includeCharts' => false,
      'to_ascii' => true,
      'encoding' => 
      array (
        'input' => 'UTF-8',
        'output' => 'UTF-8',
      ),
      'calculate' => true,
      'ignoreEmpty' => false,
      'force_sheets_collection' => false,
      'dates' => 
      array (
        'enabled' => true,
        'format' => false,
        'columns' => 
        array (
        ),
      ),
      'sheets' => 
      array (
        'test' => 
        array (
          'firstname' => 'A2',
        ),
      ),
    ),
    'views' => 
    array (
      'styles' => 
      array (
        'th' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'strong' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'b' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'i' => 
        array (
          'font' => 
          array (
            'italic' => true,
            'size' => 12,
          ),
        ),
        'h1' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 24,
          ),
        ),
        'h2' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 18,
          ),
        ),
        'h3' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 13.5,
          ),
        ),
        'h4' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 12,
          ),
        ),
        'h5' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 10,
          ),
        ),
        'h6' => 
        array (
          'font' => 
          array (
            'bold' => true,
            'size' => 7.5,
          ),
        ),
        'a' => 
        array (
          'font' => 
          array (
            'underline' => true,
            'color' => 
            array (
              'argb' => 'FF0000FF',
            ),
          ),
        ),
        'hr' => 
        array (
          'borders' => 
          array (
            'bottom' => 
            array (
              'style' => 'thin',
              'color' => 
              array (
                0 => 'FF000000',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);
