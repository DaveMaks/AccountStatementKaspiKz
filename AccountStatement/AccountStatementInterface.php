<?php

namespace AccountStatement;

abstract class AccountStatementInterface
{

    var $filePath;
    var $content;

    abstract function getContent();

    abstract function getTable();
}