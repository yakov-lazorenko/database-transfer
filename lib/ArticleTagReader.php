<?php


class ArticleTagReader extends SimpleDataReader
{
    public $article_tag = null;

    
    public function readData()
    {
        if (!$this->readArticlesTags()) {
            $this->_echo("fail : readArticlesTags()");
            return false;
        }

        return true;
    }



    public function getData()
    {
    	return [
            'article_tag' => $this->article_tag,
    	];
    }



	public function readArticlesTags()
	{
	    $rows = [];

        $rows = $this->db_connection->selectAll("

            SELECT DISTINCT `tag_id`, `article_id`

            FROM `tag_article`

        ");

        if ( $rows === NULL ) {
            return false;
        }

        $this->article_tag = $rows;

	    return true;
	}

}
