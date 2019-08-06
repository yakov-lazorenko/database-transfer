<?php


class CategoryWriter extends SimpleDataWriter
{

    public function writeData()
    {
        if (!$this->writeCategories()) {
            $this->_echo("fail : writeCategories()");
            return false;
        }

        if (!$this->writeCategoriesTags()) {
            $this->_echo("fail : writeCategoriesTags()");
            return false;
        }

        return true;
    }


    public function truncateTables()
    {
        $query = "
        SET FOREIGN_KEY_CHECKS = 0;
        SET AUTOCOMMIT = 0;
        START TRANSACTION;

        TRUNCATE `categories`;
        TRUNCATE `categories_translations`;
        TRUNCATE `category_tag`;

        SET FOREIGN_KEY_CHECKS = 1;
        COMMIT;
        SET AUTOCOMMIT = 1 ;
        ";

        return $this->db_connection->exec($query);
    }



    public function writeCategories()
    {
        try {
	        foreach ($this->inputData['categories_tree'] as $parent) {
	            $this->writeCategory($parent);
	            $this->writeCategoryTranslations($parent);

	        	if (isset($parent['children'])) {
	        	    foreach ($parent['children'] as $child) {
			            $this->writeCategory($child);
			            $this->writeCategoryTranslations($child);
	        	    }
	        	}
	        }

        } catch (Exception $e) {
            return false;
        }

        return true;
    }



    public function writeCategoriesTags()
    {
        try {
            $valuesRows = [];

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

	        foreach ($this->inputData['categories_tags'] as $ct) {

                $tag_id = $ct['tag_id'];

                $category_id = $ct['category_id'];

                $valuesRows[] = "( $tag_id, $category_id )";

	        }

            $valuesRows = implode(', ' , $valuesRows);

            $query = "
                INSERT INTO `category_tag` (
                    `tag_id`, `category_id`
                )
                VALUES $valuesRows ;
            ";

            $this->db_connection->pdo->exec($query);
            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
        } catch (Exception $e) {
            return false;
        }

        return true;
    }



    public function writeCategory($category)
    {
		$id = $category['id'];
		$parent_id = $category['pid'];
		$alias = $category['alias'];
		$position = $category['position'];
		$position_main = $category['position'];
		$active = $category['active'];

        if ($alias == 'main-page-category'){
        	$alias = 'home-page-tags-category';
        }

        $query = "
            INSERT INTO `categories` (
                `id`, `parent_id`, `alias`, `position`, `position_main`, 
                `active`
            )
            VALUES (
                $id, $parent_id, '$alias', $position, $position_main,
                $active
            );
        ";

        if (!$this->db_connection->execHard($query)){
            throw new Exception('error : writeCategory()');
        }

        return $this->db_connection->lastInsertId();
    }



    public function writeCategoryTranslations($category)
    {

    	if (empty($category['translations'])) {
            return null;
        }

		$category_id = $category['id'];

        foreach ($category['translations'] as $locale => $t) {
            $title = $t['title'];
            $description = $t['description'];
	        $meta_title = $t['meta_title'];
	        $meta_keywords = $t['meta_keywords'];
	        $meta_description = $t['meta_description'];

	        $query = "
	            INSERT INTO `categories_translations` ( 
	                `category_id`, `locale`, `title`, `text_full`, 
	                `meta_title`, `meta_keywords`, `meta_description`
	            )
	            VALUES (
	                $category_id, '$locale', '$title', '$description', 
	                '$meta_title', '$meta_keywords', '$meta_description'
	            );
	        ";

	        if (!$this->db_connection->execHard($query)) {
	            throw new Exception('error : writeCategoryTranslations()');
	        }

        }

        return $this->db_connection->lastInsertId();
    }



    public function writeCategoryTag($category_tag)
    {
        $tag_id = $category_tag['tag_id'];
        $category_id = $category_tag['category_id'];

        $query = "
            INSERT INTO `category_tag` ( 
                `tag_id`, `category_id`
            )
            VALUES (
                $tag_id, $category_id
            );
        ";

        if (!$this->db_connection->execHard($query)) {
            throw new Exception('error : writeCategoryTranslations()');
        }

        return $this->db_connection->lastInsertId();
    }


}
