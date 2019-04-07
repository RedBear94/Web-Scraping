<?php
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
	$packtPage = curlGet('https://www.yandex.ru/search/?text=новости&lr=2&clid=1882611');
	echo $packtPage;
?>