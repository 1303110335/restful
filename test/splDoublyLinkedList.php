<?php


$ddl = new SplDoublyLinkedList();
$ddl->push(2);
$ddl->push(3);
//$ddl->pop();
$ddl->unshift(99);//在头部加入
//$ddl->shift();//移除

echo 'bottom:', $ddl->bottom() . "<br>";
echo 'top:', $ddl->top() . "<br>";
echo 'count:', $ddl->count() . "<br>";
echo 'key:', $ddl->key() . "<br>";
echo 'offsetGet(1):', $ddl->offsetGet(1) . "<br>";
echo 'offsetExists(1):', $ddl->offsetExists(1) . "<br>";

$ddl->rewind();
while($ddl->valid()) {
    echo $ddl->current() . PHP_EOL;
    $ddl->next();
}


//var_dump($ddl);