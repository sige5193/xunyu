<?php
namespace app\operators;
interface IOperator {
    function setCmdArgs( $args );
    function init();
    function start ();
    function stop ();
    function destory ();
}