<?php

namespace AccountStatement;

use \Smalot\PdfParser\Parser;

class Kaspi_kz extends AccountStatementInterface
{

    function __construct($FilePath)
    {
        $this->filePath = $FilePath;
        $this->getContent();

    }

    function getContent()
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($this->filePath);
        $this->content = $pdf->getText();
    }

    function getTable()
    {
        $line = explode("\n", $this->content);

        // Находим строку начала таблици
        $indexStart = 0;
        foreach ($line as $inx => $item) {
            if (strpos($item, 'Дата Сумма Операция') !== false) {
                $indexStart = $inx + 1;
                break;
            }
        }
        // Перебераем таблицу
        $return = [];
        for ($i = $indexStart; $i < count($line); $i++) {
            $reg = "/(?'date'\d{2}\.\d{2}\.\d{2}) (?'sum'[+-]? ([\d ,₸]+))(?'action'Покупка|Перевод|Пополнение|Снятие) (?'details'.+)/";
            if (preg_match($reg, $line[$i], $matches)) {
                $item = new ItemTable();
                $item->Date = $matches['date'];
                $_sum = str_replace([' ', '₸'], '', $matches['sum']);
                $_sum = (float)str_replace(',', '.', $_sum);
                $item->Sum = $_sum;
                $item->Operation = $matches['action'];
                $item->Details = trim($matches['details']);
                $return[] = $item;
            }
        }
        return $return;
    }

}
