<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 13/09/2017
 * Time: 2:05 PM
 */

namespace Althea\Customer\Plugin;

use Magento\Customer\Model\Address;

class AddressAfter
{

    public function afterGetDataModel(Address $subject, \Magento\Customer\Model\Data\Address $result)
    {
        if (!$subject->getCustomer()->getDefaultBillingAddress()
            || $result->getId() != $subject->getCustomer()->getDefaultBillingAddress()->getId()) {

            $result->setIsDefaultBilling(false);
        }

        if (!$subject->getCustomer()->getDefaultShippingAddress()
            || $result->getId() != $subject->getCustomer()->getDefaultShippingAddress()->getId()) {

            $result->setIsDefaultShipping(false);
        }

        return $result;
    }

}