<?php



class ArticleTagWriter extends SimpleDataWriter
{



    public function writeData()
    {

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

        TRUNCATE `article_tag`;

        SET FOREIGN_KEY_CHECKS = 1;
        COMMIT;
        SET AUTOCOMMIT = 1 ;
        ";

        return $this->db_connection->exec($query);

    }






    public function writeArticlesTags()
    {

        try {

            $valuesRows = [];

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

            foreach ($this->inputData['article_tag'] as $at) {

                $tag_id = $at['tag_id'];
                $article_id = $at['article_id'];

                $valuesRows[] = "( $tag_id, $article_id )";

            }

            $valuesRows = implode( ', ' , $valuesRows );

            $query = "

                INSERT INTO `article_tag` ( 
                    `tag_id`, `article_id`
                )

                VALUES $valuesRows ;

            ";

            $this->db_connection->pdo->exec($query);

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

        } catch ( Exception $e ){

            return false;

        }


        return true;

    }



}
