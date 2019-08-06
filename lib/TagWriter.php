<?php


class TagWriter extends SimpleDataWriter
{
    // translatable columns in tags tables
    public $translatableColumns = '
        title alias meta_title meta_keywords meta_description
    ';



    public function writeData()
    {
        if (!$this->writeTags()) {
            $this->_echo("fail : writeTags()");
            return false;
        }

        if (!$this->writeTagsTranslations()) {
            $this->_echo("fail : writeTagsTranslations()");
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

        TRUNCATE `tags`;
        TRUNCATE `tags_translations`;

        SET FOREIGN_KEY_CHECKS = 1;
        COMMIT;
        SET AUTOCOMMIT = 1 ;
        ";

        return $this->db_connection->exec($query);
    }




    public function writeTags()
    {
        try {
            $columnsArr = ['id'];
            $columnsStr = [];

            foreach ($columnsArr as $column) {
                $columnsStr[] = "`$column`";
            }

            $columnsStr = implode(', ', $columnsStr);
            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');
            $valuesRows = [];

            foreach ($this->inputData['tags'] as $row) {
                $valuesStr = [];
                foreach ($columnsArr as $column) {
                    $valuesStr[] = $row[ $column ];
                }

                $valuesStr = implode(', ', $valuesStr);
                $valuesRows[] = '( ' . $valuesStr . ' )';
            } // foreach

            $valuesRows = implode(', ' , $valuesRows);

            $query = "

                    INSERT INTO `tags`

                    ( $columnsStr )

                    VALUES $valuesRows ;

                ";

            $this->db_connection->pdo->exec($query);
            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

        } catch (PDOException $e){
            return false;
        }

        return true;
    }



    public function writeTagsTranslations()
    {
        $translatableColumnsArr = $this->getTranslatableColumnsArr();

        try {
            $columnsArr = array_merge(['tag_id', 'locale'], $translatableColumnsArr);
            $columnsStr = [];

            foreach ($columnsArr as $column) {
                $columnsStr[] = "`$column`";
            }

            $columnsStr = implode(', ', $columnsStr);
            $valuesRows = [];
            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

            foreach ($this->inputData['tags'] as $row) {
                if (!count( $row['translations'])){
                    continue;
                }

                foreach ($row['translations'] as $translation) {

                    $translation['tag_id'] = $row['id'];
                    $valuesStr = [];

                    foreach ($columnsArr as $column) {
                        $value = $translation[ $column ];
                        $valuesStr[] = $this->db_connection->pdo->quote($value);
                    }

                    $valuesStr = implode(', ', $valuesStr);
                    $valuesRows[] = '( ' . $valuesStr . ' )';
                } // foreach ( $row['translations'] as $translation ) {
            } // foreach ($this->inputData['tags'] as $row) {

            $valuesRows = implode( ', ' , $valuesRows);

            $query = "

                INSERT INTO `tags_translations`

                ( $columnsStr )

                VALUES $valuesRows ;

            ";

            $this->db_connection->pdo->exec($query);
            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
        } catch (PDOException $e){
            return false;
        }

        return true;
    }



    public function getTranslatableColumnsArr()
    {
        $columns = preg_replace("/[[:space:]]+/", ' ', $this->translatableColumns);
        return explode(' ', trim($columns));
    }

}
