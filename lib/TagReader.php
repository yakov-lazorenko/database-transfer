<?php



class TagReader extends SimpleDataReader
{

    public $tags = null;
    
    // translatable columns in tags tables in old DB
    public $translatableColumns = '
        title alias meta_title meta_keywords meta_description
    ';



    public function readData()
    {

        if ( ! $this->readTags() ) {

            $this->_echo("fail : readTags()");

            return false;
        }

        return true;

    }





    public function getData()
    {
    	return [
            'tags' => $this->tags,
    	];
    }










	public function readTags()
	{

	    $rows = [];

        $translatable_columns = $this->getTranslatableColumnsArr();
        $translation_prefix = 't_';

        foreach ($translatable_columns as $value) {

            $columns_str[]  =
                '`t`.'. '`' . $value . '` AS `' . $value . '`';

            $translation_columns_str[]  =
                '`tt`.'. '`' . $value . '` AS `' . $translation_prefix . $value . '`';
        }

        $translation_columns_str = implode(', ', $translation_columns_str);
        $columns_str = implode(', ', $columns_str);

        try {

            $query = "

                SELECT
                `t`.`id` AS `id`,
                `t`.`language_id` AS `language_id`,
                `tt`.`language_id` AS `t_language_id`,
                $columns_str,
                $translation_columns_str

                FROM `tag` AS `t`

                LEFT JOIN `tag_translate` AS `tt` ON `tt`.`tag_id` = `t`.`id`

                ORDER BY `t`.`id` ASC

            ";

            $stmt = $this->db_connection->pdo->query($query);

            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

                $rows[] = $this->prepareInputData($row);

            }


        } catch ( PDOException $e ) {

            return false;

        }



        $this->tags = $rows;

	    return true;

	}








    public function prepareInputData($row)
    {

        $data['id'] = $row['id'];
        $translatable_columns = $this->getTranslatableColumnsArr();
        $translation_prefix = 't_';

        $locale = $this->localeCode[ $row['language_id'] ] ;
        $translations[ $locale ]['locale'] = $locale;

        foreach ($translatable_columns as $column) {

            $translations[ $locale ][ $column ] = $row[ $column ];

        }

        if ( isset( $row['t_language_id'] ) && 
            ( $row['t_language_id'] != $row['language_id'] ) ) {

            $locale = $this->localeCode[ $row['t_language_id'] ];
            $translations[ $locale ]['locale'] = $locale;

            foreach ($translatable_columns as $column) {

                $translations[ $locale ][ $column ] = $row[ $translation_prefix . $column ];

            }

        }

        $data['translations'] = $translations;

        return $data;

    }








    public function getTranslatableColumnsArr()
    {

        return get_words_array_from_str($this->translatableColumns);

    }



}

