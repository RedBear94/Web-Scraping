<?php

function vardump($var) {
  echo '<pre>';
  var_dump($var);
  echo '</pre>';
}

// Функция для получения запроса GET с использованием cURL
function curlGet($url) {
	$ch = curl_init(); // Инициализация сессии cURL
	// Настройка параметров cURL
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url);
	$results = curl_exec($ch); // Выполнение сеанса cURL
	curl_close($ch); // Закрытие сессии cURL
	return $results; // Вернуть результаты
}

// Объявление массивов
$mixedEmails = array();
$validEmails = array();
$mixedLinks = array();
$validLinks = array();

// Функция возврата объекта XPath
function returnXPathObject($item) {
	$xmlPageDom = new DomDocument();	// Создание нового объекта DomDocument
	@$xmlPageDom->loadHTML($item);	// Загрузка HTML с загруженной страницы
	// @ - инструктирует процедуру игнорировать любые найденные ошибки
	// это сделано, потому что бывают случаи когда HTML-файл в Интернете будет содержать недопустимую разметку
	$xmlPageXPath = new DOMXPath($xmlPageDom);	// Создание нового объекта XPath DOM
	return $xmlPageXPath;	// Возвращение объекта XPath
}

$packtPage = curlGet('https://www.ulmart.ru/help/spb/contacts');	// Функция вызова curlGet и сохранение возвращаемых результатов в переменной $packtPage

$packtPageXpath = returnXPathObject($packtPage);

$scrapedEmails = $packtPageXpath->query('//a');	// Запрос всех ссылок на странице

// Если результаты
if ($scrapedEmails->length > 0) {
	// Для каждого результата
	for ($i = 0; $i < $scrapedEmails->length; $i++) {
		$mixedEmails[] = $scrapedEmails->item($i)->nodeValue;	// Добавить результат в массив $mixedEmails
	}
}

// For each result in $mixedEmails array
foreach ($mixedEmails as $key => $email) {
	// Если результатом является валидный адрес электронной почты
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$validEmails[] = $email;	// Добавить адрес электронной почты в массив $validEmails (в соответствии с RFC 2396)
	}
}

$scrapedLinks = $packtPageXpath->query('//a/@href');	// Запрос атрибута href для всех привязок ссылок

// Если результаты
if ($scrapedLinks->length > 0) {
	// Для каждого результата
	for ($j = 0; $j < $scrapedLinks->length; $j++){
		$mixedLinks[] = $scrapedLinks->item($j)->nodeValue;	// Добавить результат в массив $mixedLinks
	}
}

// Для каждого результата в $ mixedLinks array
foreach ($mixedLinks as $key => $link) {
	// Если результатом является валидная ссылка
	if (filter_var($link, FILTER_VALIDATE_URL)) {
		$validLinks[] = $link;	// Добавить ссылку в массив $validLinks
	}
}

vardump($validLinks);	// Печать массива проверенных ссылок

vardump($validEmails);	// Печать массива подтвержденных писем

?>