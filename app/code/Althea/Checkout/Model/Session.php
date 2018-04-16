<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/02/2018
 * Time: 12:08 PM
 */

namespace Althea\Checkout\Model;

use Magento\Framework\Session\SessionManager;

/**
 * althea:
 * - MOLPay will clear checkout session regardless success / failed payment
 * - that is why we better use our own session for mobile redirection
 *
 * Class Session
 *
 * @package Althea\Checkout\Model
 */
class Session extends SessionManager {

}