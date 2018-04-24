<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 21:38
 */

$pdo = new \PDO('mysql:host=127.0.0.1;dbname=mydb;', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
/*$stmt = $pdo->prepare();
$stmt->execute();*/
return $pdo;