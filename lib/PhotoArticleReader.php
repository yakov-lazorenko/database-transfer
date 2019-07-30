<?php



class PhotoArticleReader extends AdvancedReader
{

    // settings for old DB :

    public $entityTableName = 'avatar';

    public $entityTranslationTableName = 'avatar_translate';

    public $translatableColumns = ['title', 'source', 'url'];

    // entity id column name in translation table in old DB
    public $entityIdColumnName = 'avatar_id';




    // prepare data from QSL SELECT query (in old DB)
    // to writing in NEW DB
    // массив данных на выходе должен содержать элементы с ключами, 
    // названия которых совпадают с именами столбцов в новой БД
    public function prepareEntityData($data)
    {

        // in NEW DB
        $translatableColumns = ['source', 'title'];

        $entity['id'] = $data['id'];
        $entity['url'] = $data['url'];

        $entity['translations'] = $this->prepareEntityTranslations($data, $translatableColumns);

        return $entity;

    }





}



/*




    SELECT

    `e`.`id` AS `id`,
    `e`.`language_id` AS `language_id`,


    `et`.`language_id` AS `t_language_id`,
    `et`.`id` AS `translation_id`,
    `et`.`title` AS `t_title`

    FROM `avatar` AS `e`

    LEFT JOIN (
        SELECT `avatar_id`, MAX(`id`) AS `id`
        FROM `avatar_translate`
        GROUP BY `avatar_id`
    ) AS `et_unique`
        ON `et_unique`.`avatar_id` = `e`.`id`

    LEFT JOIN `avatar_translate` AS `et`
        ON `et`.`id` = `et_unique`.`id`

    ORDER BY `id` ASC;
3889


    SELECT

    `e`.`id` AS `id`,
    `e`.`language_id` AS `language_id`,


    `et`.`language_id` AS `t_language_id`,
    `et`.`id` AS `translation_id`,
    `et`.`title` AS `t_title`

    FROM `avatar` AS `e`

    LEFT JOIN `avatar_translate` AS `et`

        ON `et`.`avarat_id` = `e`.`id`

    WHERE `t_language_id` IS NULL

    ORDER BY `id` ASC;
3157




    


*/

/*

SELECT
    max(id),
    GROUP_CONCAT(language_id separator ',' ) as language_ids,
    GROUP_CONCAT(id separator ',' ) as translation_ids,
    count(avatar_id) as c 
FROM `avatar_translate` 
group by avatar_id 
having c > 1 ;
10

 max(id)    language_ids    translation_ids     c   
47  2,2     46,47   2
97  2,2     96,97   2
407     2,2     406,407     2
487     2,2     486,487     2
583     2,2     582,583     2
983     2,2     982,983     2
1444    2,2     1443,1444   2
1461    2,2     1460,1461   2
2688    2,2     2687,2688   2
2690    2,2     2689,2690   2








SELECT count(photo_article_id) as c FROM `photos_articles_translations` 
GROUP BY photo_article_id having c = 1 
732



SELECT

    `e`.`id` AS `id`,
    `e`.`language_id` AS `language_id`,


    `et`.`language_id` AS `t_language_id`


    FROM `avatar` AS `e`

    LEFT JOIN `avatar_translate` AS `et` 
        ON `et`.`avatar_id` = `e`.`id`

    WHERE `et`.`language_id` IS NULL

ORDER BY `t_language_id` ASC;
732

*/
