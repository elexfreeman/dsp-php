<?php

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-Type: application/x-msexcel");
header("Content-Disposition: attachment; filename=patients.csv;");
header("Content-Transfer-Encoding:­ binary");

//echo "Фамилия;Имя;Отчество;Дата рождения;Адрес регистрации;СНИЛС;ЛПУ;Дата закрытия"." \n";
foreach($patients as $p)
{
    foreach($p as $key=>$value)
    {
        echo $key.';';
    }
    echo "\n";
    break;
}


 foreach($patients as $p) {
    foreach($p as $key=>$value)
    {
        echo iconv('UTF-8', 'CP1251', $p[$key]).';';
    }
     echo "\n";

 }