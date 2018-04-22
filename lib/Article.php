<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 21:39
 */

namespace spl\lib;
use spl\lib\ErrorCode;
class Article
{

    private $_db;

    public function __construct($_db)
    {
        $this->_db = $_db;
    }

    /**
     * 创建文章
     * @param $title
     * @param $content
     * @param $userId
     * @throws \Exception
     */
    public function create($title, $content, $userId)
    {
        if (empty($title)) {
            throw  new \Exception('文章标题不能为空', ErrorCode::ARTICLE_TITLE_CANNOT_EMPTY);
        }

        if (empty($content)) {
            throw  new \Exception('文章内容不能为空', ErrorCode::ARTICLE_CONTENT_CANNOT_EMPTY);
        }

        $sql = "INSERT INTO `article` (`title`, `content`, `userId`, `created_at`) VALUES(:title,:content,:userId,:created_at)";
        $created_at = time();
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':created_at', $created_at);
        if (!$stmt->execute()) {
            throw new \Exception('文章创建失败', ErrorCode::ARTICLE_CREATE_FAILED);
        }
        return [
            'articleId' => $this->_db->lastInsertId(),
            'title' => $title,
            'content' => $content,
            'userId' => $userId,
            'createdAt' => $created_at
        ];
    }

    /**
     * 编辑文章
     * @param $articleId
     * @param $title
     * @param $content
     * @param $userId
     * @return array|mixed
     * @throws \Exception
     */
    public function edit($articleId, $title, $content, $userId)
    {
        $article = $this->view($articleId);
        if ($article['userId'] !== $userId) {
            throw new \Exception('您无权编辑改文章', ErrorCode::PERMISSION_DENIED);
        }
        $title = empty($title) ? $article['title'] : $title;
        $content = empty($content) ? $article['content'] : $content;
        if ($title === $article['title'] && $content === $article['content']) {
            return $article;
        }

        $sql = 'UPDATE `article` SET `title`=:title,`content`=:content WHERE `articleId`=:articleId';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':articleId', $articleId);
        if (!$stmt->execute()) {
            throw new \Exception('文章编辑失败', ErrorCode::ARTICLE_EDIT_FAILED);
        }

        return [
            'articleId' => $articleId,
            'title' => $title,
            'content' => $content,
            'userId' => $userId
        ];
    }

    /**
     * 删除文章
     * @param $articleId
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function delete($articleId, $userId)
    {
        $article = $this->view($articleId);
        if ($article['userId'] != $userId) {
            throw new \Exception('您无权删除该文章', ErrorCode::PERMISSION_DENIED);
        }

        $sql = 'DELETE FROM `article`  WHERE `articleId`=:articleId AND `userId` =:userId';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':articleId', $articleId);
        $stmt->bindParam(':userId', $userId);
        if (false === $stmt->execute()) {
            throw new \Exception('文章删除失败', ErrorCode::ARTICLE_DELETE_FAILED);
        }
        return true;
    }

    /**
     * @param $articleId
     * @return mixed
     * @throws \Exception
     */
    public function view($articleId)
    {
        if (empty($articleId)) {
            throw new \Exception('文章ID不能为空', ErrorCode::ARTICLEID_NOT_EMPTY);
        }

        $sql = 'SELECT * FROM `article` where articleId = :articleId';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':articleId', $articleId);
        $stmt->execute();
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (empty($article)) {
            throw new \Exception('文章不存在', ErrorCode::ARTICLE_NOT_EXIST);
        }
        return $article;
    }

    /**
     * 获取文章列表
     * @param $userId
     * @param int $page
     * @param int $size
     * @return mixed
     */
    public function getList($userId, $page = 1, $size = 10)
    {
        $sql = 'SELECT * FROM `article` WHERE `userId`=:userId LIMIT :limit, :offset';
        $stmt = $this->_db->prepare($sql);
        $limit = ($page - 1) * $size;
        $limit = $limit < 0 ? 0 : $limit;
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':limit', $limit);
        $stmt->bindParam(':offset', $size);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

}