<?php

namespace Althea\Customer\Model;

use Magento\Customer\Setup\CustomerSetupFactory;
use Althea\Customer\Api\CustomerAttributesInterface;

use Magento\Customer\Model\Customer;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerAttributes implements CustomerAttributesInterface
{
    protected $attributeHelper;
    protected $searchResultsFactory;

    protected $customerFactory;
    protected $customerResourceFactory;

    public function __construct(\Clarion\CustomerAttribute\Helper\Customerattribute $attributeHelper,
                                \Magento\Framework\Api\SearchResultsInterface $searchResults,
                                \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory,
                                \Magento\Customer\Model\CustomerFactory $customerFactory) {
        $this->attributeHelper = $attributeHelper;
        $this->searchResultsFactory = $searchResults;
        $this->customerFactory = $customerFactory;
        $this->customerResourceFactory = $customerResourceFactory;
    }

    public function saveAttributeValue($customerId, $attribute_code, $value)
    {
        $result_items = [];
        $searchResults = $this->searchResultsFactory;

        if (is_null($value) || empty($value)) {
            array_push($result_items, ["result" => "FALSE"]);

            $searchResults->setItems($result_items);
            $searchResults->setTotalCount(1);

            return $searchResults;
        }

        $customer = $this->customerFactory->create();
        $customerData = $customer->getDataModel();
        $customerData->setId($customerId);

        if ($this->attributeHelper->isAttribureForCustomerAccountCreate($attribute_code)) {
            $r = $customerData->getCustomAttribute($attribute_code);

            $customerData->setCustomAttribute($attribute_code, $value);
            $customer->updateData($customerData);

            $customerResource = $this->customerResourceFactory->create();
            $customerResource->saveAttribute($customer, $attribute_code);

            array_push($result_items, ["result" => "TRUE"]);
            $searchResults->setItems($result_items);
        } else {
            array_push($result_items, ["result" => "FALSE"]);
            $searchResults->setItems($result_items);
        }

        $searchResults->setTotalCount(1);

        return $searchResults;
    }

    public function getAttributes()
    {
        $attributeCollection = $this->attributeHelper->getUserDefinedAttribures();
        $searchResults = $this->searchResultsFactory;

        $item_count = 0;
        $eachItem = [];

        foreach ($attributeCollection as $attribute) {
            if ($this->attributeHelper->isAttribureForCustomerAccountCreate($attribute->getAttributeCode())) {

                $options = [];

                if ($attribute->getFrontendInput() == 'multiselect') {
                    $options = $this->attributeHelper->getAttributeOptions($attribute->getAttributeCode());
                } else  if ($attribute->getFrontendInput() == 'select') {
                    $options = $this->attributeHelper->getAttributeOptions($attribute->getAttributeCode());
                }

                array_push($eachItem, [
                        'frontEndLabel' => $attribute->getStoreLabel($this->attributeHelper->getStoreId()),
                        'note' => $attribute->getNote(),
                        'frontInputType' => $attribute->getFrontendInput(),
                        'attributeCode' => $attribute->getAttributeCode(),
                        'fieldFrontendClass' => $attribute->getFrontendClass(),
                        'isRequired' => ($attribute->getIsRequired() ? 'true' : 'false'),
                        'attribute_options' => $options
                    ]
                );

                $item_count++;

                $searchResults->setItems($eachItem);
                $searchResults->setTotalCount($item_count);
            }
        }
        return $searchResults;
    }
}