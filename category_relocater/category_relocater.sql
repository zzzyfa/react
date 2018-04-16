DROP PROCEDURE rewrite_path;
 
DELIMITER //
CREATE PROCEDURE rewrite_path(IN rootcategoryName VARCHAR(255) , IN categoryName VARCHAR(255)) 
proc_label:BEGIN
	DECLARE done INT DEFAULT 0; 
	DECLARE path_string  VARCHAR(32);
	
	Set @_entity_id_under  = (Select entity_id From catalog_category_entity_varchar where catalog_category_entity_varchar.value = binary(categoryName) and store_id = 0);
	Set @_entity_id  = (Select entity_id From catalog_category_entity_varchar where catalog_category_entity_varchar.value = binary(rootcategoryName) 
	and store_id = 0 limit 0,1 );
   	
   	IF @_entity_id Is Null OR  @_entity_id_under Is Null THEN
   		LEAVE proc_label;
   	END IF;
   	
   	Set @_parent_id = (Select parent_id From catalog_category_entity where entity_id = @_entity_id);
    Set @_path  = (Select path From catalog_category_entity where entity_id = @_entity_id);
  
	Set @old_root = (select SUBSTRING_INDEX(SUBSTRING_INDEX(@_path, '/', 3) ,'/',-1));
  
  	Update catalog_category_entity Set parent_id = @_entity_id where entity_id = @_entity_id_under;
  	
    SET @path_string = '';
    SET @new_level = 1;
   	myloop: WHILE TRUE DO
   		IF @_parent_id = 1 OR (@_parent_id is NULL) THEN
   			SET @path_string = CONCAT('1/',@path_string, @_entity_id );
   			LEAVE myloop;
   		END IF; 
   		
   		SET @path_string = CONCAT(@_parent_id,  '/', @path_string);
   		SET @_parent_id = (Select parent_id From catalog_category_entity where entity_id = @_parent_id);
   		
   		SET @new_level = @new_level + 1;
   		   		 	
   		if @new_level > 10 THEN
   			SET @path_string = CONCAT('1/',@path_string, @_entity_id );
   			LEAVE myloop;
   		END IF;

   	END WHILE myloop; 
   
	Select @path_string , path, CONCAT(@path_string, SUBSTRING(path, INSTR(path, CONCAT('/',@_entity_id_under)) ,length(path))) From catalog_category_entity 
	Where path like CONCAT('%/',@_entity_id_under) Or path like CONCAT('%/',@_entity_id_under,'/%');
  
    Update catalog_category_entity set path = CONCAT(@path_string, SUBSTRING(path, INSTR(path, CONCAT('/',@_entity_id_under)),length(path)))  
    Where path like CONCAT('%/',@_entity_id_under) Or path like CONCAT('%/',@_entity_id_under,'/%');
    
    Update catalog_category_entity set catalog_category_entity.level =  ROUND ((LENGTH(path) - LENGTH( REPLACE (path, "/", ""))) / LENGTH("/"))  
    Where path like CONCAT('%/',@_entity_id_under) Or path like CONCAT('%/',@_entity_id_under,'/%');

END //
DELIMITER ;

call rewrite_path('Shop Beauty', 'Makeup');
call rewrite_path('Shop Beauty', 'Korea\'s Trendy');
call rewrite_path('Shop Beauty', 'Hair & Body');
call rewrite_path('Shop Beauty', 'Skincare');







