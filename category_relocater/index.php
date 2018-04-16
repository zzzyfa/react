<?php
/**
 * Created by PhpStorm.
 * User: JUNGHO PARK
 * Date: 27/02/2018
 * Time: 6:02 PM
 */

require __DIR__ . '../../app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$app = $bootstrap->createApplication('RelocaterScript');
$bootstrap->run($app);
