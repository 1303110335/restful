<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 23:19
 */

namespace spl\restful;

use spl\lib\Article;
use spl\lib\User;

/**
 * Class Restful
 * @package spl\restful
 */
class Restful
{
    private $_user;

    private $_article;

    /**
     * 请求的饿方法名称
     * @var
     */
    private $_requestMethod;

    /**
     * 请求的资源名称
     * @var
     */
    private $_resourceName;

    /**
     * 请求的资源id
     * @var
     */
    private $_id;

    /**
     * 允许请求的资源列表
     * @var array
     */
    private $_allowResources = ['users', 'articles'];

    /**
     * 允许请求的method方法
     * @var array
     */
    private $_allowResquestMethods = ['GET', 'POST','PUT', 'DELETE', 'OPTIONS'];

    /**
     * 常用状态
     * @var array
     */
    private $_statusCode = [
        200 => 'OK',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allow',
        500 => 'Server Internal Error'
    ];

    public function __construct(User $_user, Article $_article)
    {
        $this->_article = $_article;
        $this->_user = $_user;
    }

    public function run()
    {
        try {
            $this->_setupRequestMethod();
            $this->_setupResource();
            if ($this->_resourceName == 'users') {
                $this->_handleUsers();
            } else {
                $this->_handleArticles();
            }
        } catch (\Exception $e) {
            $this->_json( [$e->getMessage()], $e->getCode());
        }
    }

    /**
     * 请求用户资源
     */
    private function _handleUsers()
    {
        if ($this->_requestMethod != 'POST') {
            throw  new \Exception('请求方法不被允许', 100);
        }
    }

    /**
     * 请求文章资源
     */
    private function _handleArticles()
    {

    }

    /**
     * 初始化请求方法
     * @throws \Exception
     */
    private function _setupRequestMethod()
    {
        $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
        if (!in_array($this->_requestMethod, $this->_allowResquestMethods)) {
            throw new \Exception($this->_statusCode[405], 405);
        }
    }

    /**
     * 初始化请求资源
     * @throws \Exception
     */
    private function _setupResource()
    {
        $path = $_SERVER['PATH_INFO'];
        $params = explode('/', $path);
        $this->_resourceName = $params[1];
        if (!in_array($this->_resourceName, $this->_allowResources)) {
            throw new \Exception($this->_statusCode[403], 403);
        }
        if (!empty($params[2])) {
            $this->_id = $params[2];
        }
    }


    /**
     * 输出json
     * @param $array
     */
    private function _json($array, $code)
    {
        if ($code > 0 && $code != 200 && $code != 204) {
            header("HTTP/1.1 " . $code . " " . $this->_statusCode[$code]);
        }
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        exit();
    }
}

$pdo = require_once '../lib/DB.php';
require_once '../lib/User.php';
require_once '../lib/ErrorCode.php';
require_once '../lib/Article.php';

$article = new Article($pdo);
$user = new User($pdo);
$restful = new Restful($user, $article);
$restful->run();