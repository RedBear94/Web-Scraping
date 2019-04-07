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

// Функция возврата объекта XPath
function returnXPathObject($item) {
	$xmlPageDom = new DomDocument();	// Создание нового объекта DomDocument
	@$xmlPageDom->loadHTML($item);	// Загрузка HTML с загруженной страницы
	// @ - инструктирует процедуру игнорировать любые найденные ошибки
	// это сделано, потому что бывают случаи когда HTML-файл в Интернете будет содержать недопустимую разметку
	$xmlPageXPath = new DOMXPath($xmlPageDom);	// Создание нового объекта XPath DOM
	return $xmlPageXPath;	// Возвращение объекта XPath
}

// Вызов функции curlGet() и сохранение возвращенных результатов в переменной $packtPage
$packtPage = curlGet('https://www.bookvoed.ru/book?id=7077651');	

$packtPageXpath = returnXPathObject($packtPage);	// Создание нового объекта XPath DOM

$coverImage = $packtPageXpath->query('//img[@class="sf"]/@src');	// Запрос на адрес изображения

// Если изображение существует
if ($coverImage->length > 0) {
	// Необходимо дописать https://www.bookvoed.ru т.к. возвращается неполный адрес: /files/1836/38/05/74/1.jpeg 
	$imageUrl = "https://www.bookvoed.ru".$coverImage->item(0)->nodeValue;	// Добавить URL в переменную 
	echo $imageUrl;
	
	//В новых версиях php end() ожидает переменную, а не ссылку. Поэтому $imageName = end(explode('/', $imageUrl));
	//не является переменной, это назначение. Что повлечет за собой ошибку.
	//Согласно документации:
	//Этот массив передается по ссылке, поскольку он модифицируется функцией. Это означает, что вы должны передать ему
	//реальную переменную, а не функцию, возвращающую массив, потому что только
	//фактические переменные могут передаваться по ссылке.
	
	$p = explode('/', $imageUrl); // !!!
	$imageName = end($p);	// Получение имени изображения из URL-адреса
	
	// Если файл является изображением
	if (getimagesize($imageUrl)) {
		$imageFile = curlGet($imageUrl);	// Загрузить изображение с помощью cURL
		$file = fopen($imageName, 'w');	// Открытие дескриптора файла
		fwrite($file, $imageFile);	// Запись файла изображения
		fclose($file);	// Закрытие дескриптора файла
	}
}

?>