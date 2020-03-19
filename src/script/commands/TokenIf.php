<?php
namespace app\script\tokens;
use app\script\TokenIfCondition;
class TokenIf {
    /**
     * @var TokenIfCondition[]
     */
    private $conditions = array();
    private $other = null;
}