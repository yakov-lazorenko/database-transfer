<?php


// этот класс читает статьи со старой базы (со старого сайта)
// и связи статей с тэгами
class ArticleAdvancedReader extends AdvancedReader
{
    public $articles = null;

    // common columns for tables `article` and `article_translate`
    // in old DB
    public $oldDbArticleCommonColumns = '
        language_id category_id avatar_id date_pub avatar_title title alias 
        text_short text_full gallery_id
        special_id meta_title meta_keywords meta_description
        slider_main slider_category slider_main_weight slider_category_weight
        date_stop_slider_main date_stop_slider_category
        active views advertising video creator_id editor_id author_id 
    ';

    // translatable article columns in old DB
    // that corresponds translatable columns in new DB
    public $translatableOldDbArticleColumns = '
        date_pub avatar_title title alias 
        text_short text_full
        meta_title meta_keywords meta_description
        slider_main slider_category slider_main_weight slider_category_weight
        date_stop_slider_main date_stop_slider_category
        active views advertising video creator_id editor_id author_id 
    ';


    public function readData()
    {        
        if (!$this->readArticles()) {
            $this->_echo("fail : readArticles()");
            return false;
        }

        return true;
    }



    public function getData()
    {
        return [
            'articles' => $this->articles,
        ];
    }



    public function getDataSize()
    {
        try {
            $stmt = $this->db_connection->pdo->query("

                SELECT COUNT(*) FROM `article`

            ");

            $row = $stmt->fetch();

            if (isset ($row[0])) {
                return $row[0];
            }
        } catch (PDOException $e) {
            //
        }

        return 0;
    }



    public function readArticles()
    {
        $offset = $this->dataOffset;
        $limit = $this->dataLimit;

        $articles = [];
        $common_columns = $this->getOldDbArticleCommonColumnsArr();
        $translation_prefix = 't_';

        foreach ($common_columns as $value) {
            $columns_str[]  =
                '`a`.'. '`' . $value . '` AS `' . $value . '`';

            $translation_columns_str[]  =
                '`at`.'. '`' . $value . '` AS `' . $translation_prefix . $value . '`';
        }

        $translation_columns_str = implode(', ', $translation_columns_str);
        $columns_str = implode(', ', $columns_str);

        try {
            $stmt = $this->db_connection->pdo->query("

                SELECT
                `a`.`id` AS `id`,
                `a`.`language_id` AS `language_id`,
                $columns_str,
                $translation_columns_str

                FROM `article` AS `a`

                LEFT JOIN `article_translate` AS `at` ON `at`.`article_id` = `a`.`id`

                ORDER BY `a`.`id` ASC

                LIMIT $limit OFFSET $offset

            ");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $articles[] = $this->prepareInputData($row);
            }

        } catch (PDOException $e) {
            return false;
        }

        $this->articles = $articles;
        return true;
    }



    public function prepareInputData($row)
    {
        $article['id'] = $row['id'];
        $article['category_id'] = $row['category_id'];
        $article['photo_article_id'] = $row['avatar_id'];
        $article['special_id'] = $row['special_id'];
        $article['gallery_id'] = $row['gallery_id'];

        $translatable_columns = $this->getTranslatableColumnsArr();
        $translation_prefix = 't_';

        $locale = $this->localeCode[ $row['language_id'] ] ;
        $translations[ $locale ]['locale'] = $locale;

        foreach ($translatable_columns as $column) {
            if ( $column == 'avatar_title'){
                $translations[ $locale ][ 'photo_title' ] = $row[ $column ];
            } else {
                $translations[ $locale ][ $column ] = $row[ $column ];
            }

        }

        if (isset( $row['t_language_id']) && 
           ($row['t_language_id'] != $row['language_id'])
        ) {
            $locale = $this->localeCode[ $row['t_language_id'] ];
            $translations[ $locale ]['locale'] = $locale;

            foreach ($translatable_columns as $column) {
                if ($column == 'avatar_title') {
                    $translations[ $locale ][ 'photo_title' ] = $row[ $translation_prefix . $column ];
                } else {
                    $translations[ $locale ][ $column ] = $row[ $translation_prefix . $column ];
                }
            }
        }

        $article['translations'] = $translations;
        return $article;
    }



    // get common columns for tables `article` and `article_translate`
    // in old DB
    public function getOldDbArticleCommonColumnsArr()
    {
        $common_columns = preg_replace("/[[:space:]]+/", ' ', $this->oldDbArticleCommonColumns);

        return explode(' ', trim($common_columns) );
    }



    public function getTranslatableColumnsArr()
    {
        $columns = preg_replace("/[[:space:]]+/", ' ', $this->translatableOldDbArticleColumns);
        return explode(' ', trim($columns) );
    }


}
