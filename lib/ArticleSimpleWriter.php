<?php



class ArticleSimpleWriter extends SimpleDataWriter
{

    // colums in new DB that is in input data
    public $translatableColumns = '
        creator_id editor_id author_id
        date_pub photo_title
        title alias text_short text_full
        meta_title meta_keywords meta_description
        views active
        slider_main slider_category
        slider_main_weight slider_category_weight
        date_stop_slider_main date_stop_slider_category
        advertising video
    ';

    public $quotableTranslationColumns = '
        locale photo_title
        title alias text_short text_full
        meta_title meta_keywords meta_description
    ';

    public $timestampColumns = '
        date_pub date_stop_slider_main date_stop_slider_category
    ';

    public function writeData()
    {


        if ( ! $this->writeArticles() ) {

            $this->_echo("fail : writeArticles()");

            return false;
        }



        if ( ! $this->writeArticlesTranslations() ) {

            $this->_echo("fail : writeArticles()");

            return false;
        }



        if ( ! $this->writeArticlesTags() ) {

            $this->_echo("fail : writeArticlesTags()");

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

        TRUNCATE `articles`;
        TRUNCATE `articles_translations`;
        TRUNCATE `article_tag`;

        SET FOREIGN_KEY_CHECKS = 1;
        COMMIT;
        SET AUTOCOMMIT = 1 ;
        ";

        return $this->db_connection->exec($query);

    }






    public function writeArticles()
    {

        try {


            $columnsArr = [
                'id', 'category_id', 'photo_article_id', 
                'special_id', 'gallery_id'
            ];

            $columnsStr = [];

            foreach ($columnsArr as $column) {

                $columnsStr[] = "`$column`";

            }

            $columnsStr = implode(', ', $columnsStr);


            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

            foreach ($this->inputData['articles'] as $article) {

                if ( ! $article['special_id'] ){
                    $article['special_id'] = 'null';
                }

                if ( ! $article['gallery_id'] ){
                    $article['gallery_id'] = 'null';
                }

                $valuesStr = [];

                foreach ($columnsArr as $column) {

                    $valuesStr[] = $article[ $column ];

                }

                $valuesStr = implode(', ', $valuesStr);

                $query = "

                    INSERT INTO `articles`

                    ( $columnsStr )

                    VALUES ( $valuesStr )

                ";

                $this->db_connection->pdo->exec($query);

            } // foreach ($this->inputData['articles'] as $article)

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');


        } catch ( PDOException $e ){

            return false;

        }

        return true;

    }





    public function writeArticlesTranslations()
    {

        $translatableColumnsArr = $this->getTranslatableColumnsArr();
        $quotableColumns = get_words_array_from_str($this->quotableTranslationColumns);
        $timestampColumns = get_words_array_from_str($this->timestampColumns);

        try {


            $columnsArr = array_merge(
                ['article_id', 'locale'], $translatableColumnsArr );

            $columnsStr = [];

            foreach ($columnsArr as $column) {

                $columnsStr[] = "`$column`";

            }

            $columnsStr = implode(', ', $columnsStr);


            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

	        foreach ($this->inputData['articles'] as $article) {

                if ( ! count( $article['translations'] ) ){
                    continue;
                }

                foreach ( $article['translations'] as $translation ) {

                    $translation['article_id'] = $article['id'];

                    if ( ! $translation['author_id'] ){
                        $translation['author_id'] = 'null';
                    }

                    $valuesStr = [];

                    foreach ($columnsArr as $column) {

                        $value = $translation[ $column ];

                        if ( in_array($column, $quotableColumns) ) {

                            $valuesStr[] = $this->db_connection->pdo->quote($value);

                        } elseif ( in_array($column, $timestampColumns) ) {

                             $valuesStr[] = "FROM_UNIXTIME($value)";

                        } else {

                            $valuesStr[] = $value;

                        }

                    }

                    $valuesStr = implode(', ', $valuesStr);

                    $query = "

                        INSERT INTO `articles_translations`

                        ( $columnsStr )

                        VALUES ( $valuesStr )

                    ";

                    $this->db_connection->pdo->exec($query);

                } // foreach ( $article['translations'] as $translation )

	        } // foreach ($this->inputData['articles'] as $article)

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

        } catch ( PDOException $e ){

            return false;

        }

        return true;

    }












    public function writeArticlesTags()
    {

        try {

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

	        foreach ($this->inputData['articles_tags'] as $at) {

                $tag_id = $at['tag_id'];
                $article_id = $at['article_id'];

                $query = "
                    INSERT INTO `article_tag` ( 
                        `tag_id`, `article_id`
                    )
                    VALUES (
                        $tag_id, $article_id
                    )
                ";

                $this->db_connection->pdo->exec($query);                

	        }

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

        } catch ( Exception $e ){

            return false;

        }

        return true;

    }









    public function getTranslatableColumnsArr()
    {

        $columns = preg_replace("/[[:space:]]+/", ' ', $this->translatableColumns);

        return explode(' ', trim($columns) );

    }



}
