<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 21:39
 */

namespace spl\lib;
use spl\lib\ErrorCode;

class User
{
    /**
     * 数据库连接句柄
     * @var
     */
    private $_db;

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * @param mixed $db
     */
    public function setDb($db)
    {
        $this->_db = $db;
    }

    public function __construct($pdo)
    {
        $this->setDb($pdo);
    }

    /**
     * 用户登录
     * @param $username
     * @param $password
     * @throws  \Exception
     * @return array
     */
    public function login($username, $password)
    {
        if (empty($username)) {
            throw new \Exception('用户名不能为空', ErrorCode::USERNAME_CANNOT_EMPTY);
        }

        if (empty($password)) {
            throw new \Exception('密码不能为空', ErrorCode::PASSWORD_CANNOT_EMPTY);
        }

        $sql = 'SELECT * FROM `user` WHERE `username` = :username AND `password` = :password';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $password = $this->_md5($password);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (empty($user)) {
            throw new \Exception('用户名或密码错误', ErrorCode::USERNAME_OR_PASSWORD_INVALID);
        }
        unset($user['password']);
        return $user;
    }

    /**
     * 注册用户
     * @param $username
     * @param $password
     * @throws \Exception
     */
    public function register($username, $password)
    {
        if (empty($username)) {
            throw new \Exception('用户名不能为空', ErrorCode::USERNAME_CANNOT_EMPTY);
        }

        if ($this->_isUsernameExists($username)) {
            throw new \Exception('用户名已存在', ErrorCode::USERNAME_EXISTS);
        }

        if (empty($password)) {
            throw new \Exception('密码不能为空', ErrorCode::PASSWORD_CANNOT_EMPTY);
        }

        //写入数据库
        $sql = 'INSERT INTO `user` (`username`,`password`,`created_at`) VALUES (:username, :password, :created_at);';
        $createdAt = time();
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $password = $this->_md5($password);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':created_at', $createdAt);

        var_dump($stmt->queryString);exit;
        if (!$stmt->execute()) {
            throw new \Exception('用户注册失败', ErrorCode::USER_REGSITER_FAILED);
        }
        return [
            'userId' => $this->_db->lastInsertId(),
            'username' => $username,
            'createdAt' => $createdAt
        ];
    }


    private function _debugQuery($queryString)
    {
        //INSERT INTO `user` (`username`,`password`,`created_at`) VALUES (:username, :password, :created_at);

    }

    private function _md5($string, $key = 'imooc')
    {
        return md5($string . $key);
    }

    /**
     * 检测用户名是否存在
     * @param $username
     * @return bool
     */
    private function _isUsernameExists($username)
    {
        $sql = 'select * from `user` where `username` =:username';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return !empty($result);
    }
}