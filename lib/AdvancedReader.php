<?php


class AdvancedReader implements DataReader
{
    use ConsoleEcho;

	const LANG_RU = 1;
	const LANG_UK = 2;

    // если есть многоязычность, то значение этой переменной должно быть true
    public $withTranslations = true;


    // settings for old DB :

    public $entityTableName = ''; // 'entity';

    public $entityTranslationTableName = ''; // = 'entity_translate';

    public $translatableColumns = []; // = ['col1', 'col2', 'col3'];

    // entity id column name in translation table in old DB
    public $entityIdColumnName = ''; // = 'entity_id';

    public $translation_prefix = 't_';


    // названия столбцов, которые нужно прочитать и перенести в новую БД
    // используется только в режиме без переводов ( $this->withTranslations == false )
    public $columns = [];

    
    public $localeCode = [ self::LANG_RU => 'ru', self::LANG_UK => 'uk' ];
    public $db_connection = null;
    public $settings = null;

    public $dataOffset = 0;
    public $dataLimit = 0;

    public $entities = null;

    public $transferManager = null;



    public function init($settings)
    {
        $this->settings = $settings;
    }


    public function run()
    {
        if (!$this->readData()) {
            $this->_echo( "fail : readData()" );
            return false;
        }
        return true;
    }



    public function getData()
    {
        return [
            'entities' => $this->entities,
        ];
    }



    public function getDataSize()
    {
        $entityTableName = $this->entityTableName;

        try {
            $stmt = $this->db_connection->pdo->query("

                SELECT COUNT(*) FROM `$entityTableName`

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



    public function readData()
    {
        if ($this->withTranslations) {
            if (!$this->readEntitiesWithTranslations()) {
                $this->_echo("fail : readEntitiesWithTranslations()");
                return false;
            }
        } else {
            if (!$this->readEntities()) {
                $this->_echo("fail : readEntities()");
                return false;
            }
        }
        return true;
    }



    public function initDataBlock($offset, $limit)
    {
        $this->dataOffset = $offset;
        $this->dataLimit = $limit;
        $this->entities = null;
    }



    public function connectToDb()
    {
        $this->db_connection = new DatabaseConnection;

        if (!$this->db_connection->connect( $this->settings )) {
            $this->_echo($this->db_connection->error);
            return false;
        }

        return true;
    }



    public function disconnectFromDb()
    {
        $this->db_connection->disconnect();
    }



    // чтение данных с переводами
    public function readEntitiesWithTranslations()
    {
        // entity tables in old DB
        $entityTableName = $this->entityTableName;
        $entityTranslationTableName = $this->entityTranslationTableName;

        $translatableColumns = $this->translatableColumns;

        // entity id column name in translation table in old DB
        $entityIdColumnName = $this->entityIdColumnName;

        $translation_prefix = $this->translation_prefix;

        $offset = $this->dataOffset;
        $limit = $this->dataLimit;

        foreach ($translatableColumns as $column) {

            // translatable columns names in entity table in old DB
            $entityTableColumns[]  =
                '`e`.'. '`' . $column . '` AS `' . $column . '`';

            // translatable columns names in TRANSLATION entity table in old DB
            $translationTableColumns[]  =
                '`et`.'. '`' . $column . '` AS `' . $translation_prefix . $column . '`';
        }

        $entityTableColumns = implode(', ', $entityTableColumns);
        $translationTableColumns = implode(', ', $translationTableColumns);

        $entities = [];

        try {
            $query = "

                SELECT

                `e`.`id` AS `id`,

                `e`.`language_id` AS `language_id`,

                $entityTableColumns ,

                `et`.`language_id` AS `{$translation_prefix}language_id`,

                $translationTableColumns

                FROM `$entityTableName` AS `e`

                LEFT JOIN (

                    SELECT `$entityIdColumnName`, MAX(`id`) AS `id`

                    FROM `$entityTranslationTableName`

                    GROUP BY `$entityIdColumnName`

                ) AS `et_unique`

                    ON `et_unique`.`$entityIdColumnName` = `e`.`id`

                LEFT JOIN `$entityTranslationTableName` AS `et`

                    ON `et`.`id` = `et_unique`.`id`

                ORDER BY `id` ASC

                LIMIT $limit OFFSET $offset ;
            ";

            $stmt = $this->db_connection->pdo->query($query);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $entities[ $row['id'] ] = $this->prepareEntityData($row);
            }

        } catch (PDOException $e) {
            return false;
        }

        $this->entities = $entities;
        return true;
    }



    // чтение данных без переводов
    public function readEntities()
    {
        // entity tables in old DB
        $entityTableName = $this->entityTableName;
        $offset = $this->dataOffset;
        $limit = $this->dataLimit;

        if ($this->columns) {
            foreach ($this->columns as $column) {
                // columns names in entity table in old DB
                $entityTableColumns[]  =
                    '`e`.'. '`' . $column . '` AS `' . $column . '`';
            }
            $entityTableColumns = implode(', ', $entityTableColumns);
        } else {
            $entityTableColumns = ' * ';
        }

        $entities = [];

        try {
            $query = "

                SELECT

                $entityTableColumns

                FROM `$entityTableName` AS `e`

                ORDER BY `e`.`id` ASC

                LIMIT $limit OFFSET $offset ;

            ";

            $stmt = $this->db_connection->pdo->query($query);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $entities[ $row['id'] ] = $this->prepareEntityData($row);
            }

        } catch (PDOException $e) {
            return false;
        }

        $this->entities = $entities;

        return true;
    }



    public function prepareEntityData($data)
    {
        if ($this->withTranslations) {
            $data['translations'] = $this->prepareEntityTranslations($data, $this->translatableColumns);
        }

        return $data;
    }



    // $translatableColumns (массив строк) - столбцы с 
    // переводимыми (многоязычными) данными (с таблиц старой БД), 
    // которые нужно записать в новую БД
    //
    // $renamedColumns (массив) - столбцы которые нужно переименовать 
    // при записи в новую БД, формат : [ 'OldDbColumnName1' => 'NewDbColumnName1' , 
    // 'OldDbColumnName2' => 'NewDbColumnName2' , ...
    public function prepareEntityTranslations($data, $translatableColumns, $renamedColumns = null)
    {
        $translation_prefix = $this->translation_prefix;
        $locale = $this->localeCode[ $data['language_id'] ] ;
        $translations[ $locale ]['locale'] = $locale;

        foreach ($translatableColumns as $column) {
            if (empty( $renamedColumns )) {
                $translations[ $locale ][ $column ] = $data[ $column ];
            } else {
               if (in_array( $column, array_keys($renamedColumns) )) {
                   $translations[ $locale ][ $renamedColumns[ $column ] ] = $data[ $column ];
               } else {
                   $translations[ $locale ][ $column ] = $data[ $column ];
               }
            }
        }

        if (isset($data[ $translation_prefix . 'language_id' ]) &&
           ($data[ $translation_prefix . 'language_id' ] != $data['language_id'])
        ) {
            $locale = $this->localeCode[ $data[ $translation_prefix . 'language_id' ] ];
            $translations[ $locale ]['locale'] = $locale;

            foreach ($translatableColumns as $column) {
                if (empty($renamedColumns)) {
                    $translations[ $locale ][ $column ] = $data[ $translation_prefix . $column ];
                } else {
                   if (in_array($column, array_keys($renamedColumns))) {
                       $translations[ $locale ][ $renamedColumns[ $column ] ] = $data[ $translation_prefix . $column ];
                   } else {
                       $translations[ $locale ][ $column ] = $data[ $translation_prefix . $column ];
                   }
                }
            } // foreach ($translatableColumns as $column) {

        } // if ( isset(

        return $translations;
    }


}
