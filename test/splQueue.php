<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 20:35
 */

//$q = new SplQueue();
//$q->push(1);
//$q->push(2);
//$q->push(3);
//$q->pop();
////var_dump($q->isEmpty());
//print_r($q);


$fruits = array(
    "apple" => "yummy",
    "orange" => "ah ya, nice",
    "grape" => "wow, I love it!",
    "plum" => "nah, not me"
);

$obj = new ArrayObject($fruits);
$it = $obj->getIterator();
echo "Iterating over:" . $obj->count() . "<br>";

//$obj->append("haha");
//$obj->ksort();
$obj->asort();

while($it->valid()) {
    echo $it->key() . '=' . $it->current(). "<br>";
    $it->next();
}


/*foreach ($it as $key => $value) {
    echo $key . '=' . $value . "<br>";
}*/


