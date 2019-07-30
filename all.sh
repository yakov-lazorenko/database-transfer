#!/bin/bash

# this sсript transfers data from old DB to new DB
# DB configs located in file 'bootstrap.php'

php transfer-archive-articles.php ;

php transfer-archive-photos.php ;

php transfer-articles.php ;

php transfer-article-tag.php ;

php transfer-photo-articles.php ;

php transfer-categories.php ;

php transfer-tags.php ;

php transfer-users.php ;

php transfer-contests.php ;

php transfer-galleries.php ;

php transfer-special-themes.php ;

php transfer-static-pages.php ;

