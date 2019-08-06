<?php


class AdvancedWriter implements DataWriter
{
    use ConsoleEcho;

    // если есть многоязычность, то равно true
    public $withTranslations = true;


    // settings for NEW DB :
    public $entityTableName = ''; // 'entity';

    // используется только в режиме с переводами ( $this->withTranslations == true )
    public $entityTranslationTableName = ''; // = 'entity_translate';

    // названия столбцов, которые нужно записать в новую БД
    public $entityTableAllColumns = [];

    // in translation table (new DB)
    public $translatableColumns = []; // = ['col1', 'col2', 'col3'];

    // столбцы, значения которых нужно взять в кавычки
    public $quotableColumnsInEntityTable = [];

    public $quotableColumnsInTranslationTable = [];


    // columns that has a timestamp type
    public $timestampColumnsInEntityTable = [];

    public $timestampColumnsInTranslationTable = [];


    // используется только в режиме с переводами ( $this->withTranslations == true )
    // entity id column name in translation table in old DB
    public $entityIdColumnName = ''; // = 'entity_id';

    public $db_connection = null;

    public $settings = null;

    public $inputData = null;

    public $transferManager = null;



    public function init($settings)
    {
        $this->settings = $settings;
    }



    public function run()
    {
		if (!$this->writeData()) {
		    $this->_echo( "fail : writeData()" );
            return false;
		}

        return true;
    }



    public function setInputData($data)
    {
    	$this->inputData = $data;
    }



    public function truncateTables()
    {
        if ($this->withTranslations) {
            $query = "
                SET FOREIGN_KEY_CHECKS = 0;
                SET AUTOCOMMIT = 0;
                START TRANSACTION;

                TRUNCATE `{$this->entityTableName}`;
                TRUNCATE `{$this->entityTranslationTableName}`;

                SET FOREIGN_KEY_CHECKS = 1;
                COMMIT;
                SET AUTOCOMMIT = 1 ;
            ";

        } else {

            $query = "
                SET FOREIGN_KEY_CHECKS = 0;
                SET AUTOCOMMIT = 0;
                START TRANSACTION;

                TRUNCATE `{$this->entityTableName}`;

                SET FOREIGN_KEY_CHECKS = 1;
                COMMIT;
                SET AUTOCOMMIT = 1 ;
            ";

        }

        return $this->db_connection->exec($query);
    }



    public function writeData()
    {
        if (!$this->writeEntities()) {
            $this->_echo("fail : writeEntities()");
            return false;
        }

        if ($this->withTranslations) {
            if (!$this->writeEntitiesTranslations()) {
                $this->_echo("fail : writeEntitiesTranslations()");
                return false;
            }
        }

        return true;
    }



    public function connectToDb()
    {
        $this->db_connection = new DatabaseConnection;

        if (!$this->db_connection->connect( $this->settings)){
            $this->_echo($this->db_connection->error);
            return false;
        }

        return true;
    }



    public function disconnectFromDb()
    {
        $this->db_connection->disconnect();
    }



    public function writeEntities()
    {
        // in New DB
        $entityTableName = $this->entityTableName;
        $timestampColumns = $this->timestampColumnsInEntityTable;
        $quotableColumns = $this->quotableColumnsInEntityTable;

        try {
            $columnsStr = [];

            foreach ($this->entityTableAllColumns as $column) {
                $columnsStr[] = "`$column`";
            }

            $columnsStr = implode(', ', $columnsStr);
            $valuesRows = [];

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

            foreach ($this->inputData['entities'] as $entity) {
                $valuesStr = [];
                foreach ($this->entityTableAllColumns as $column) {
                        $value = $entity[ $column ];
                        if (in_array($column, $quotableColumns)) {
                            $valuesStr[] = $this->db_connection->pdo->quote($value);
                        } elseif (in_array($column, $timestampColumns)) {
                            $valuesStr[] = "FROM_UNIXTIME($value)";
                        } else {
                            $valuesStr[] = $value;
                        }
                }

                $valuesStr = implode(', ', $valuesStr);
                $valuesRows[] = '( ' . $valuesStr . ' )';
            } // foreach ($this->inputData['

            $valuesRows = implode( ', ' , $valuesRows );

            $query = "

                INSERT INTO `$entityTableName`

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



    public function writeEntitiesTranslations()
    {
        $timestampColumns = $this->timestampColumnsInTranslationTable;
        $quotableColumns = $this->quotableColumnsInTranslationTable;

        try {
            $columnsArr = array_merge([$this->entityIdColumnName, 'locale'], $this->translatableColumns);
            $columnsStr = [];

            foreach ($columnsArr as $column) {
                $columnsStr[] = "`$column`";
            }

            $columnsStr = implode(', ', $columnsStr);
            $valuesRows = [];
            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

            foreach ($this->inputData['entities'] as $entity) {
                if (!count($entity['translations'])){
                    continue;
                }

                foreach ($entity['translations'] as $translation) {
                    $translation[ $this->entityIdColumnName ] = $entity['id'];
                    $valuesStr = [];

                    foreach ($columnsArr as $column) {
                        $value = $translation[ $column ];
                        if ( $column == 'locale'){
                            $valuesStr[] = $this->db_connection->pdo->quote($value);
                            continue;
                        }

                        if (in_array($column, $quotableColumns)) {
                            $valuesStr[] = $this->db_connection->pdo->quote($value);
                        } elseif ( in_array($column, $timestampColumns) ) {
                            $valuesStr[] = "FROM_UNIXTIME($value)";
                        } else {
                            $valuesStr[] = $value;
                        }
                    }

                    $valuesStr = implode(', ', $valuesStr);
                    $valuesRows[] = '( ' . $valuesStr . ' )';
                } // foreach ( $entity['translations']
            } // foreach ($this->inputData['entities']

            $valuesRows = implode(', ' , $valuesRows);

            $query = "

                INSERT INTO `{$this->entityTranslationTableName}`

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

}
