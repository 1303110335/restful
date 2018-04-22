<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 21:22
 */

namespace spl;

use spl\lib\Article;
use spl\lib\User;

$pdo = require_once 'lib/DB.php';
require_once 'lib/User.php';
require_once 'lib/ErrorCode.php';
require_once 'lib/Article.php';

//$user = new User($pdo);
//print_r($user->register('admin2', 'admin'));
//print_r($user->login('admin2', 'admin'));
$article = new Article($pdo);
//print_r($article->create('文章标题', '文章内容', 4));
//print_r($article->create('文章标题2', '文章内容2', 4));
//print_r($article->create('文章标题3', '文章内容3', 4));
//print_r($article->create('文章标题4', '文章内容4', 4));
//print_r($article->view(1));
//print_r($article->edit(1, '文章标题4', '文章内容2', 4));
//print_r($article->delete(1, 4));
print_r(json_encode($article->getList(4, 1, 4)));

