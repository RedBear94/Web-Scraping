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

// Функция для очистки содержимого между двумя строками
function scrapeBetween($item, $start, $end) {
	// stripos - Возвращает позицию первого вхождения подстроки без учета регистра.
	if (($startPos = stripos($item, $start)) === false) {	// Если $start не найдена
		return false;	// Return false
	} else if (($endPos = stripos($item, $end)) === false) {	// Если строка $end  не найдена
		return false;	// Return false
	} else {
		$substrStart = $startPos + strlen($start);	// Назначение начальной позиции
		return substr($item, $substrStart, $endPos - $substrStart);	// Возвращаемая строка между начальным и конечным позициями
	}
}

// Получение информации о статусе сайта
$page = curlGet('https://cdn.syndication.twimg.com/timeline/profile?callback=__twttr.callbacks.tl_i0_profile_rianru_new&dnt=false&domain=ria.ru&lang=ru&min_position=1067387794677473280&screen_name=rianru&suppress_response_codes=true&t=1714800&tz=GMT%2B0300&with_replies=false');
$statusInfo = scrapeBetween($page,'"status":',',"');
echo $statusInfo."<br>";

// Получение информации о лицензии
$page = curlGet('https://platform.twitter.com/widgets/widget_iframe.c9b0d6e1ef0320c49dc875c581cc9586.html?origin=https%3A%2F%2Fria.ru&settingsEndpoint=https%3A%2F%2Fsyndication.twitter.com%2Fsettings');
$licenseInfo = scrapeBetween($page,'@license','
 *            ');
echo $licenseInfo;

?>