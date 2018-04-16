<?php

namespace Althea\ShopByBrand\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2') < 0) {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();

            $sql = "DROP PROCEDURE  IF EXISTS tm_brand;";
            $connection->exec($sql);

            $sql = "CREATE PROCEDURE tm_brand() 
                    BEGIN
                        DECLARE done INT DEFAULT 0; 
                        DECLARE brand_name VARCHAR(40); 
                        DECLARE url_key VARCHAR(40); 
                        DECLARE meta_title VARCHAR(40); 
                        DECLARE meta_keywords VARCHAR(40); 
                        DECLARE description VARCHAR(512); 
                        DECLARE meta_description VARCHAR(512); 
                        DECLARE eid INT; 
                        DECLARE contact_cursor CURSOR FOR 
                            SELECT entity_id FROM catalog_category_entity WHERE  path LIKE '%1/2/79/224/%';
                        DECLARE CONTINUE handler FOR NOT FOUND SET done = 1;
                        OPEN contact_cursor; 
                        TM_BRANDLOOP: 
                        LOOP 
                            FETCH contact_cursor INTO eid; 
                            IF done = 1 THEN 
                              LEAVE tm_brandloop; 
                            END IF; 
                            SET @brand_name = (SELECT value FROM catalog_category_entity_varchar WHERE entity_id = eid AND attribute_id = 41 AND store_id = 0); 
                            SET @url_key = (SELECT value FROM catalog_category_entity_varchar WHERE entity_id = eid AND attribute_id = 43 AND store_id = 0); 
                            SET @description = (SELECT value FROM catalog_category_entity_text WHERE entity_id = eid AND attribute_id = 44 AND store_id = 0); 
                            SET @meta_description = (SELECT value FROM catalog_category_entity_text WHERE entity_id = eid AND attribute_id = 48 AND store_id = 0); 
                            SET @meta_title = (SELECT value FROM catalog_category_entity_varchar WHERE entity_id = eid AND attribute_id = 46 AND store_id = 0); 
                            SET @meta_keywords = (SELECT value FROM catalog_category_entity_text WHERE entity_id = eid AND attribute_id = 47 AND store_id = 0); 
                            INSERT INTO tm_brand 
                                        (brand_id,
                                         name, 
                                         status, 
                                         url_key, 
                                         title, 
                                         logo, 
                                         brand_banner, 
                                         product_banner, 
                                         short_description, 
                                         main_description, 
                                         meta_keywords, 
                                         meta_description, 
                                         website_ids) 
                            VALUES      ( eid,
                                          @brand_name, 
                                          1, 
                                          @url_key, 
                                          @meta_title, 
                                          \"\", 
                                          \"\", 
                                          \"\", 
                                          \"\", 
                                          @description, 
                                          @meta_keywords, 
                                          @meta_description, 
                                          \"1,2,3,4,5,6\" ); 
                         
                          END LOOP tm_brandloop; 
                          CLOSE contact_cursor; 
                        END; ";

            //$connection->exec("truncate table tm_brand;");
            //$connection->exec("truncate table tm_brand_product;");
            $connection->exec($sql);
            $connection->exec("call tm_brand();");
            //$connection->exec("Delete from catalog_product_entity_int Where attribute_id = 216;");

            $connection->exec("INSERT INTO catalog_product_entity_int 
            (attribute_id, 
             store_id, 
             entity_id, 
             value) 
                    SELECT t.attribute_id, 
                           0, 
                           entity_id, 
                           brand_id 
                    FROM  
                          (SELECT attribute_id FROM eav_attribute Where attribute_code = 'brand_id') t, 
                          (SELECT * 
                            FROM   catalog_product_entity_int 
                            WHERE  attribute_id = 138 
                                   AND store_id = 0) AS A, 
                           (SELECT b.value     AS 'brand_name', 
                                   a.option_id AS 'value', 
                                   c.brand_id 
                            FROM   eav_attribute_option a, 
                                   eav_attribute_option_value b, 
                                   tm_brand c 
                            WHERE  a.option_id = b.option_id 
                                   AND BINARY c.name = b.value 
                                   AND a.attribute_id = 138) B 
                    WHERE  A.value = B.value; ");
        }
        $setup->endSetup();
    }
}