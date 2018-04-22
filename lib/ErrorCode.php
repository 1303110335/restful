<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/22 0022
 * Time: 21:50
 */

//便于错误的查询
namespace spl\lib;

class ErrorCode
{
    const USERNAME_EXISTS = '1';
    const PASSWORD_CANNOT_EMPTY = '2';
    const USERNAME_CANNOT_EMPTY = '3';
    const USER_REGSITER_FAILED = '4';
    const USERNAME_OR_PASSWORD_INVALID = '5';
    const ARTICLE_TITLE_CANNOT_EMPTY = '6';
    const ARTICLE_CONTENT_CANNOT_EMPTY = '7';
    const ARTICLE_CREATE_FAILED = '8';
    const ARTICLEID_NOT_EMPTY = '9';
    const ARTICLE_NOT_EXIST = '10';
    const PERMISSION_DENIED = '11';
    const ARTICLE_EDIT_FAILED = '12';
    const ARTICLE_DELETE_FAILED = '13';
}