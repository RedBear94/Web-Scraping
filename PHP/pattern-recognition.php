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
	curl_setopt($ch, CURLOPT_URL, $url);
	$results = curl_exec($ch); // Выполнение сеанса cURL
	curl_close($ch); // Закрытие сессии cURL
	return $results; // Вернуть результаты
}

// Вызов функции curlGet () и сохранение возвращенных результатов в переменной $packtContactPage
$packtContactPage = curlGet('https://www.kiabi.ru/uslugi/e-mail.html');	

// Шаблон Regex для соответствия адресам электронной почты
$emailRegex = '/([A-Za-z0-9\.\-\_\!\#\$\%\&\'\*\+\/\=\?\^\`\{\|\}]+)\@([A-Za-z0-9.-_]+)(\.[A-Za-z]{2,5})/';

// Соответствие шаблонов регулярных выражений и присвоение результатов массиву
preg_match_all($emailRegex, $packtContactPage, $scrapedEmails);

// Извлечение уникальных записей в $scrapedEmails в массив $ emailAddresses
$emailAddresses = array_values(array_unique($scrapedEmails[0]));

vardump($emailAddresses);

?>