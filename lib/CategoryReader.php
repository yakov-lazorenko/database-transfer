<?php



// этот класс читает категории со старой базы (со старого сайта)
// и связи категорий с тэгами
class CategoryReader extends SimpleDataReader
{

    private $categories = null;
    private $categoriesTree = null;
    private $categoriesTags = null;



    public function readData()
    {

		$this->readCategoriesTree();

		$this->displayCategoriesTree();

		$this->readCategoriesTags();

        return true;

    }








    public function getData()
    {
    	return [
    	    'categories' => $this->categories,
    	    'categories_tree' => $this->categoriesTree,
            'categories_tags' => $this->categoriesTags,
    	];
    }










	public function readCategoriesTree()
	{

	    $categoriesTree = [];

	    $categories = $this->readCategoriesWithTranslations();

	    $tree = [];

	    foreach ($categories as $category){
	        if ( $category['pid'] == 0 ){
	       	    $tree[ $category['id'] ] = $category;
	       	}
	    }

	    foreach ($categories as $category){
	        if ( $category['pid'] > 0 ){
	        	if ( isset( $tree[ $category['pid'] ] ) ){
	        		$tree[ $category['pid'] ]['children'][ $category['id'] ] = $category;
	        	}
	       	}
	    }

        $this->categoriesTree = $tree;
        $this->categories = $categories;

	    return $tree;

	}








	public function readCategoriesWithTranslations()
	{

	    $rows = $this->db_connection->pdo->query("

	    	SELECT

	    	c.id, c.pid, c.language_id, c.title, c.alias, c.description,
		    c.position, c.meta_title, c.meta_keywords, c.meta_description, c.active,
		    c.main_menu, c.footer_menu,

	        c_t.language_id AS t_language_id, c_t.title AS t_title,
	        c_t.alias AS t_alias, c_t.description AS t_description,
		    c_t.position AS t_position, c_t.meta_title AS t_meta_title,
		    c_t.meta_keywords AS t_meta_keywords, c_t.meta_description AS t_meta_description

	    	FROM `category` AS `c`

	    	LEFT JOIN `category_translate` AS `c_t` ON `c_t`.`category_id` = `c`.`id`

	    	WHERE `c`.`alias` != 'page-not-found'

	        ORDER BY `c`.`alias` ASC 

	    ")->fetchAll();


	    $categories = $this->prepareCategoriesInputData($rows);

	    return $categories;

	}






	public function prepareCategoriesInputData($rows)
	{

	    foreach ($rows as $row) {

	        $translation1 = [
	            'language_id' => $row['language_id'],
	            'title' => $row['title'],
	            'description' => $row['description'],
	            'meta_title' => $row['meta_title'],
	            'meta_keywords' => $row['meta_keywords'],
	            'meta_description' => $row['meta_description'],
	        ];


	        $translation2 = null;

	        if ( in_array( $row['t_language_id'], [ self::LANG_RU, self::LANG_UK ] ) ) {

		        $translation2 = [
		            'language_id' => $row['t_language_id'],
		            'title' => $row['t_title'],
		            'description' => $row['t_description'],
		            'meta_title' => $row['t_meta_title'],
		            'meta_keywords' => $row['t_meta_keywords'],
		            'meta_description' => $row['t_meta_description'],
		        ];

	        }


	        $translations['ru'] = null;
	        $translations['uk'] = null;

	        if ( $translation1['language_id'] == self::LANG_RU ){
	            $translations['ru'] =	$translation1;
	        }
	        
	        if ( $translation1['language_id'] == self::LANG_UK ){
	            $translations['uk'] =	$translation1;
	        }

	        if (
	        	isset( $translation2 ) && 
	        	( $translation2['language_id'] == self::LANG_UK ) &&
	        	( $translation1['language_id'] == self::LANG_RU )
	        ){
	            $translations['uk'] =	$translation2;
	        }

	        if (
	        	isset( $translations2 ) &&
	        	( $translations2['language_id'] == self::LANG_RU ) &&
	        	( $translations1['language_id'] == self::LANG_UK )
	        ){
	            $translations['ru'] =	$translations2;
	        }

            if ( $row['alias'] == 'konkurs' ){
            	$row['alias'] = 'contests';
            }

	        $categories[] = [
	            'id' => $row['id'],
	            'pid' => $row['pid'],
	            'alias' => $row['alias'],
	            'active' => $row['active'],
	            'position' => $row['position'],
	            'main_menu' => $row['main_menu'],
	            'footer_menu' => $row['footer_menu'],
	            'translations' => $translations,
	        ];

	    }

	    return $categories;

	}







	public function readCategoriesTags()
	{

	    $rows = $this->db_connection->pdo->query("

	    	SELECT DISTINCT t.category_id, t.tag_id

	    	FROM `tag_category` AS `t`

	        ORDER BY `t`.`category_id` ASC

	    ")->fetchAll();

	    $this->categoriesTags = $rows;

	    return $this->categoriesTags;
	}









	public function displayCategoriesTree()
	{

		$this->_echo("categories tree:");
		foreach ($this->categoriesTree as $c) {

			$id =  $c['id'];
			$alias =  $c['alias'];
		    $this->_e(" + $id - $alias \n");

		    if ( isset($c['children']) ){

				foreach ($c['children'] as $ch) {
					$id =  $ch['id'];
					$alias =  $ch['alias'];
				    $this->_e("        + $id - $alias \n");
			    }

		    }

		}

	}








	public function displayCategoriesTags()
	{

		$this->_echo("categories tags:");


	}



}






