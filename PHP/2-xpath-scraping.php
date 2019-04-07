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
	
$packtData = array();	// Объявление массива для хранения извлеченных данных
// Функция возврата объекта XPath
function returnXPathObject($item) {
	$xmlPageDom = new DomDocument();	// Создание нового объекта DomDocument
	@$xmlPageDom->loadHTML($item);	// Загрузка HTML с загруженной страницы
	// @ - инструктирует процедуру игнорировать любые найденные ошибки
	// это сделано, потому что бывают случаи когда HTML-файл в Интернете будет содержать недопустимую разметку
	$xmlPageXPath = new DOMXPath($xmlPageDom);	// Создание нового объекта XPath DOM
	return $xmlPageXPath;	// Возвращение объекта XPath
}

$packtPage = curlGet('https://www.yandex.ru');	// Вызов curlGet и сохранение возвращаемых результатов в переменной $ packtPage

$packtPageXpath = returnXPathObject($packtPage);	// Создание нового объекта XPath DOM

$rate = $packtPageXpath->query('//span[@class="inline-stocks__value_inner"]');	// Запрос на span с соотвутсвующим класом
// Если название существует
if ($rate->length > 2) {
	//echo $rate->length."<br>";
	$packtData['usd'] = $rate->item(0)->nodeValue;	// Добавление результатов в массив
	$packtData['eur'] = $rate->item(1)->nodeValue;
	$packtData['oil'] = $rate->item(2)->nodeValue;
}

$news = $packtPageXpath->query('//li/a[@class="home-link list__item-content home-link_black_yes"]');	// Запрос на новостную ленту яндекса
$i = 0;
if ($rate->length > 0){
	do {
		$packtData['news'][$i] = trim($news->item($i)->nodeValue);	// Перебор и заполнение массива из новостей 
		// trim - удаляет пробелы из начала и конца строки
		$i++;
	} while ($i != $news->length);
}

//print_r($packtData);
vardump($packtData);
?>