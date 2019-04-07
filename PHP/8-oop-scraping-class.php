<?php

require_once('scrape.class.php');	// Подключение класса
$data = new Scrape('https://ru.wikipedia.org/wiki/Регулярные_выражения');	// Создание нового экземпляра класса Scrape
$data->title = $data->xPathObj->query('//h1')->item(0)->nodeValue;			// Извлечение заголовка
echo $data->title.'<br />';	// Вывод загаловка
echo $data->baseUrl.'<br />';
/*
if (!empty($data->xPathObj->query('//title')->item(0))) {
    $data->title = $data->xPathObj->query('//title')->item(0);	// Assigning book title
	echo $data->title . '<br />';
}*/
?>