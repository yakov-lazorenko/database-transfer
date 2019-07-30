<?php



class UsersReader extends AdvancedReader
{

    public $site_users = null;
    public $cms_users = null;


    public function readCmsUsers()
    {

        $users = [];

        try {

            // get authors, admins and editors
            $query = '

                SELECT * FROM `users` AS `u`

                JOIN `users_rights` AS `ur` ON `ur`.`users_id` = `u`.`id`

                WHERE `rights_id` IN (2,3,4,6,7) ;

            ';

            $stmt = $this->db_connection->pdo->query($query);

            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

                $users[] = $this->prepareCmsUserInputData($row);

            }


        } catch ( PDOException $e ) {

            return false;

        }

        $this->cms_users = $users;

        return true;

    }





    public function prepareCmsUserInputData($data)
    {

        $cms_user = [];

        $cms_user['id'] = $data['id'];
        $cms_user['right_id'] = $this->getRightId($data['rights_id']);

        $cms_user['email'] = $data['email'];
        $cms_user['password'] = $data['password'];
        $cms_user['name'] = $data['name'];

        return $cms_user;

    }



    public function readData()
    {
        return true;
    }






    public function getData()
    {
    	return [
    	    'site_users' => $this->site_users,
            'cms_users' => $this->cms_users,
    	];
    }







	public function readSiteUsers()
	{
    /*
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

            while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

                $articles[] = $this->prepareInputData($row);

            }


        } catch ( PDOException $e ) {

            return false;

        }

        $this->articles = $articles;
    */
	    return true;

	}











    // get right id in new site (from old right_id)
    public function getRightId($right_id)
    {

        switch ($right_id) {

            case 1 : // user
                return 4;
                break;

            case 2 : // admin
                return 1;
                break;

            case 3 : // editor
                return 2;
                break;

            case 4 : // editor
                return 2;
                break;

            case 5 : // user
                return 4;
                break;

            case 6 : // moderator
                return 2;
                break;

            case 7 : // author
                return 3;
                break;

            default :
                return 4; // user
                break;

        }

    }


}
