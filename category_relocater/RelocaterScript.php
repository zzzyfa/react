<?php

class RelocaterScript extends \Magento\Framework\App\Http implements \Magento\Framework\AppInterface {
    public function launch()
    {
        $objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        $categoryFactory = $objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $categories = $categoryFactory->create()
            ->addAttributeToSelect('*');

		$new_parent_category = 479;
		$prepare_categories = ['Korea\'s Trendy','Makeup', 'Hair & Body', 'Skincare'];
		
        foreach ($categories as $category){

			if(in_array($category->getName(), $prepare_categories)) {
				print( 'processing... : '. $category->getId().'<br>');
	
				$category->move($new_parent_category,null);
                print ('done : '. $category->getId() .'<br>');
            }
        }
        
        return $this->_response;
    }

    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
}
