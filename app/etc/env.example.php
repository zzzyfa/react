<?php
return array (
    'backend' =>
        array (
            'frontName' => 'admin',
        ),
    'crypt' =>
        array (
            'key' => '35e88c0aa53c70bfe1cb46a6911bff60',
        ),
    'session' =>
        array (
            'save' => 'files',
        ),
    'db' =>
        array (
            'table_prefix' => '',
            'connection' =>
                array (
                    'default' =>
                        array (
                            'host' => 'rds-althea-magento2-test.cfuuaigkt64d.ap-southeast-1.rds.amazonaws.com:3306',
                            'dbname' => 'magento2',
                            'username' => 'master',
                            'password' => 'gayabangsar119B',
                            'active' => '1',
                        ),
                ),
        ),
    'resource' =>
        array (
            'default_setup' =>
                array (
                    'connection' => 'default',
                ),
        ),
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'cache_types' =>
        array (
            'config' => 1,
            'layout' => 1,
            'block_html' => 1,
            'collections' => 1,
            'reflection' => 1,
            'db_ddl' => 1,
            'eav' => 1,
            'customer_notification' => 1,
            'full_page' => 1,
            'config_integration' => 1,
            'config_integration_api' => 1,
            'translate' => 1,
            'config_webservice' => 1,
            'compiled_config' => 1,
        ),
    'install' =>
        array (
            'date' => 'Thu, 06 Apr 2017 06:39:43 +0000',
        ),
);
