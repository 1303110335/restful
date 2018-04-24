<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 23:19
 */

namespace spl\restful;

use spl\lib\Article;
use spl\lib\ErrorCode;
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
    private $_allowResquestMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];

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
                $this->_json($this->_handleUsers());
            } else {
                $this->_json($this->_handleArticles());
            }
        } catch (\Exception $e) {
            $this->_json([$e->getMessage()], $e->getCode());
        }
    }

    /**
     * 请求用户资源
     * @throws \Exception
     */
    private function _handleUsers()
    {
        if ($this->_requestMethod != 'POST') {
            throw  new \Exception('请求方法不被允许', 100);
        }

        $body = $this->_getBodyParams();
        if (empty($body['username'])) {
            throw new \Exception('用户名不能为空', 400);
        }
        if (empty($body['password'])) {
            throw new \Exception('密码不能为空', 400);
        }
        return $this->_user->register($body['username'], $body['password']);
    }

    /**
     * 获取请求体参数
     * @return mixed
     * @throws \Exception
     */
    private function _getBodyParams()
    {
        //获得输入的json字符串
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            throw new \Exception('请求参数错误', 400);
        }
        return json_decode($raw, true);
    }

    /**
     * 请求文章资源
     * @return array|void
     * @throws \Exception
     */
    private function _handleArticles()
    {
        switch ($this->_requestMethod) {
            case 'POST':
                return $this->_handleArticleCreate();
            case 'PUT':
                return $this->_handleArticleEdit();
            case 'DELETE':
                return $this->_handleArticleDelete();
            case 'GET':
                if (empty($this->_id)) {
                    return $this->_handleArticleList();
                } else {
                    return $this->_handleArticleView();
                }
        }
    }

    /**
     * 创建文章
     * @return array
     * @throws \Exception
     */
    private function _handleArticleCreate()
    {
        $body = $this->_getBodyParams();
        if (empty($body['title'])) {
            throw new \Exception('文章标题不能为空', 400);
        }
        if (empty($body['content'])) {
            throw new \Exception('文章内容不能为空', 400);
        }
//        var_dump($_SERVER);exit;
        $user = $this->_userLogin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

        try {
            $article = $this->_article->create($body['title'], $body['content'], $user['userId']);
            return $article;
        } catch (\Exception $e) {
            if (!in_array($e->getCode(), [
                ErrorCode::ARTICLE_TITLE_CANNOT_EMPTY,
                ErrorCode::ARTICLE_CONTENT_CANNOT_EMPTY
            ])) {
                throw new \Exception($e->getMessage(), 400);
            }
            throw new \Exception($e->getMessage(), 500);
        }
    }

    /**
     * 用户登录
     * @param $username
     * @param $password
     * @return array
     * @throws \Exception
     */
    private function _userLogin($username, $password)
    {
        try {
            return $this->_user->login($username, $password);
        } catch (\Exception $e) {
            if (in_array($e->getCode(),
                [
                    ErrorCode::USERNAME_CANNOT_EMPTY,
                    ErrorCode::PASSWORD_CANNOT_EMPTY,
                    ErrorCode::USERNAME_OR_PASSWORD_INVALID
                ])
            ) {
                throw new \Exception($e->getMessage(), 400);
            };
            throw new \Exception($e->getMessage(), 500);
        }
    }

    private function _handleArticleEdit()
    {
        $this->_article->edit();
    }

    private function _handleArticleDelete()
    {
        $this->_article->delete();
    }

    private function _handleArticleList()
    {
        $this->_article->getList();
    }

    private function _handleArticleView()
    {
        $this->_article->view();
    }

    /**
     * 初始化请求方法
     * @throws \Exception
     */
    private function _setupRequestMethod()
    {
//        echo json_encode($_SERVER);exit;
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
        $path = $_SERVER['REQUEST_URI'];
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
    private function _json($array, $code = 200)
    {
        if ($code > 0 && $code != 200 && $code != 204) {
            if (!isset($this->_statusCode[$code])) {
                $message = 'Unknow';
                $code = 200;
            } else {
                $message = $this->_statusCode[$code];
            }

            header("HTTP/1.1 " . $code . " " . $message);
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