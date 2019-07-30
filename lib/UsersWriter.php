<?php



class UsersWriter extends AdvancedWriter
{


    public function writeCmsUsers()
    {

        try {

            $valuesRows = [];

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

            foreach ($this->inputData['cms_users'] as $user) {

                extract($user);

                $passwords = $this->getPasswords();

                if ( isset( $passwords[ $email ] ) ){

                    $password = PasswordHash::makeHash( $passwords[ $email ] );

                }

                $valuesRows[] = "( $id, $right_id, '$email', '$password', CURRENT_TIMESTAMP() )";

            }

            $valuesRows = implode( ', ' , $valuesRows );

            $query = "

                INSERT INTO `users` ( 
                    `id`, `right_id`, `email`, `password`, `created_at`
                )

                VALUES $valuesRows ;

            ";

            $this->db_connection->pdo->exec($query);

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

        } catch ( PDOException $e ){

            return false;

        }


        return true;

    }











    public function writeCmsUsersTranslations()
    {


        try {

            $valuesRows = [];

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

            foreach ($this->inputData['cms_users'] as $user) {

                extract($user);

                $valuesRows[] = "( $id, 'ru', '$name' )";

            }

            $valuesRows = implode( ', ' , $valuesRows );

            $query = "

                INSERT INTO `users_translations` ( 
                    `user_id`, `locale`, `name`
                )

                VALUES $valuesRows ;

            ";

            $this->db_connection->pdo->exec($query);

            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

        } catch ( PDOException $e ){

            return false;

        }


        return true;


    }






    public function writeData()
    {/*

        if ( ! $this->writeSiteUsers() ) {

            $this->_echo("fail : writeSiteUsers()");

            return false;
        }
        */
        return true;

    }





    public function truncateTables()
    {

        $query = '
            SET FOREIGN_KEY_CHECKS = 0;
            SET AUTOCOMMIT = 0;
            START TRANSACTION;

            TRUNCATE `users`;
            TRUNCATE `users_translations`;
            -- TRUNCATE `site_users`;

            SET FOREIGN_KEY_CHECKS = 1;
            COMMIT;
            SET AUTOCOMMIT = 1 ;
        ';

        return $this->db_connection->exec($query);

    }






    public function writeSiteUsers()
    {/*

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

            $valuesRows = [];

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

                $valuesRows[] = '( ' . $valuesStr . ' )';

            } // foreach ($this->inputData['articles'] as $article)


            $valuesRows = implode( ', ' , $valuesRows );

            $query = "

                INSERT INTO `articles`

                ( $columnsStr )

                VALUES $valuesRows ;

            ";

            $this->db_connection->pdo->exec($query);

            //dd( $valuesRows );
            //dd( $query );


            $this->db_connection->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');


        } catch ( PDOException $e ){

            return false;

        }
        */
        return true;

    }


    public function getPasswords()
    {

        return [

            'aleksey.kostenko@edipresse.ua'      => 'sdjh345klq',
            'nina.voychuk@edipresse.ua'          => 'gb2v4v2236',
            'aleksandr.shkurin@edipresse.ua'     => 'nq22bxhwek',
            'svetlana.storozhuk@edipresse.ua'    => 'drgnzfd24r',
            'oksana.shevchenko@edipresse.ua'     => '9h682phab4',
            'urushan@gmail.com'                  => 'v3oq31f9zd',
            'galina.krombet@edipresse.ua'        => '37qua1d06m',
            'yuliya.volobuyeva@edipresse.ua'     => 'qb5sh5jntx',
            'alina.tatsenko@edipresse.ua'        => 't143tm6wh5',
            'nadezhda.kryukovskaya@edipresse.ua' => 'ucnhnrr46w',
            'natalya.emelina@edipresse.ua'       => '123456xam$qad',

        ];

    }



}

