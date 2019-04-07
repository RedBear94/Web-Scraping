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
	//curl_close($ch); // Закрытие сессии cURL
	return $results; // Вернуть результаты
}

// Функция возврата объекта XPath
function returnXPathObject($item) {
	$xmlPageDom = new DomDocument();	// Создание нового объекта DomDocument
	@$xmlPageDom->loadHTML($item);	// Загрузка HTML с загруженной страницы
	// @ - инструктирует процедуру игнорировать любые найденные ошибки
	// это сделано, потому что бывают случаи когда HTML-файл в Интернете будет содержать недопустимую разметку
	$xmlPageXPath = new DOMXPath($xmlPageDom);	// Создание нового объекта XPath DOM
	return $xmlPageXPath;	// Возвращение объекта XPath
}

// Функция для получения содержимого между двумя строками
function scrapeBetween($item, $start, $end) {
	if (($startPos = stripos($item, $start)) === FALSE) {	// Если строка $start не найдена
		return false;	// Вернуть ложь
	} else if (($endPos = stripos($item, $end)) === FALSE) {	// Если строка $end не найдена
		return false;	// Вернуть ложь
	} else {
		$substrStart = $startPos + strlen($start);	// Назначение стартовой позиции
		return substr($item, $substrStart, $endPos - $substrStart);	// Возвращение строки между начальной и конечной позициями
	}
}

// Объявление массивов
$resultsPages = array();
$bookPages = array();

$initialResultsPageUrl = "https://www.google.com/search?source=hp&ei=RPwLXKnwK4u7sQGuxqPgDA&q=книжные+магазины&oq=Книж&gs_l=psy-ab.1.0.0l10.5518.9261..10753...4.0..0.85.321.6......0....1..gws-wiz.....0.0HYP9jHCNWA";
$resultsPages[] = $initialResultsPageUrl;	// Добавление начального URL страницы результатов в массив $resultsPages

$initialResultsPageSrc = curlGet($initialResultsPageUrl);	// Запрос страницы начальных результатов

$resultsPageXPath = returnXPathObject($initialResultsPageSrc);	// Создание нового объекта XPath DOM

$resultsPageUrls = $resultsPageXPath->query('//tr/td/a[@class="fl"]/@href');	// Запросы для href атрибутов нумерации страниц															

// Если результаты существуют
if ($resultsPageUrls->length > 0) {
	// Для каждого URL страницы результатов
	for ($i = 0; $i < $resultsPageUrls->length; $i++) {
		$resultsPages[] = "https://www.google.com".$resultsPageUrls->item($i)->nodeValue;	// По URL страницы добавление результатов в массив $resultsPages
		echo $resultsPages[$i]."<br>";
	}
}

$uniqueResultsPages = array_values(array_unique($resultsPages));	// Удаление дубликатов из массива и переиндексация

// Для каждого уникального URL страницы результатов
foreach ($uniqueResultsPages as $resultsPage) {
	$resultsPageSrc = curlGet($resultsPage);	// Страница запроса результатов
	$booksPageXPath = returnXPathObject($resultsPageSrc);	// Создание нового объекта XPath DOM
	$bookPageUrls = $booksPageXPath->query('//div[@class="g"]/h3[@class="r"]/a/@href');	// Запросы на атрибуты href извлекаемых данных
	
	// Если существуют данные
	if ($bookPageUrls->length > 0) {
		// Для каждого URL страницы книжного магазина
		for ($i = 0; $i < $bookPageUrls->length; $i++) {
			$bookPages[] = $bookPageUrls->item($i)->nodeValue;	// Добавить URL в массив $bookPages
		}
	}
	$booksPageXPath = NULL;	// Обнуление объекта $booksPageXPath
	$bookPageUrls = NULL;	// Обнуление объекта $bookPageURLs
	sleep(rand(1, 3));	// Пауза	
}

$uniqueBookPages = array_values(array_unique($bookPages));	// Удаление дубликатов из массива и переиндексация

vardump($uniqueBookPages);

?>