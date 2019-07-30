<?php


class DatabaseConnection
{

	public $host = 'localhost';
	public $database = null;
	public $user = 'root';
	public $password = null;

    public $pdo = null;
    public $error = null;


    function __construct( $settings = null )
    {

        if ( $settings ) $this->connect($settings);

    }




    function __destruct()
    {

        $this->disconnect();

    }




	public function connect($settings)
	{

        $db = $this->database = $settings['database'];
        $host = $this->host = $settings['host'];
        $this->user = $settings['user'];
        $this->password = $settings['password'];

        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        try{

			$this->pdo = new PDO( $dsn, $this->user, $this->password);

        } catch ( PDOException $e ) {

            $this->error = 'Подключение не удалось: ' . $e->getMessage();
            $this->pdo = null;
        	return false;

        }

        return true;
    }








	public function disconnect()
	{		

		$this->pdo = null;

	}




    public function exec($query)
    {

        try{

            $this->pdo->exec($query);

        } catch ( PDOException $e ) {

            $this->error = $e->getMessage();
            $this->pdo = null;
            return false;

        }

        return true;
    }




    public function execHard($query)
    {

        $begin = "
            SET FOREIGN_KEY_CHECKS = 0;
            SET AUTOCOMMIT = 0;
            START TRANSACTION;            
        ";

        $end = "
            SET FOREIGN_KEY_CHECKS = 1;
            COMMIT;
            SET AUTOCOMMIT = 1 ;
        ";


        try{

            $this->pdo->exec( $begin . $query . $end );

        } catch ( PDOException $e ) {

            $this->error = $e->getMessage();
            $this->pdo = null;
            return false;

        }

        return true;
    }




    public function selectAll($query)
    {

        try{

            $stmt = $this->pdo->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch ( PDOException $e ) {

            $this->error = $e->getMessage();
            $this->pdo = null;
            return null;

        }

        return $result;
    }




    public function lastInsertId()
    {

        return $this->pdo->lastInsertId();

    }
    


}