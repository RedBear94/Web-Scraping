<?php
// Вывод ошибок
//error_reporting(E_ALL); 
//ini_set('display_errors', 1);

ini_set('max_execution_time', 900);	// Max время выполнения скрипта

// Массив с названиями столбцов таблицы и названий столбцов с сайта с которым будет идти работа
$mapping = array (
	// "id"			 => "",
	"title"			 => "",
	"series"		 => "Серия:",        
	"publisher"		 => "Издательство:", 
	"year"			 => "Год:",	      
	"pages"			 => "Страниц:",      
	"binding"		 => "Переплёт:",     
	"ISBN"			 => "ISBN:",         
	"dimensions"	 => "Размеры:",      
	"format"		 => "Формат:",       
	"code"			 => "Код:",          
	"inbase"		 => "В базе:",       
	"authors"		 => "Авторы:",       
	"subject"		 => "Тематика:",     
	"circulation"	 => "Тираж:",        
	"author"		 => "Автор:",        
	"translator"	 => "Переводчик:",   
);

function vardump($var) {
  echo '<pre>';
  var_dump($var);
  echo '</pre>';
}

// Функция для возврата объекта XPath
function returnXPathObject($item) {
	$xmlPageDom = new DomDocument();	// Создание нового объекта DomDocument
	@$xmlPageDom->loadHTML($item);	// Загрузка HTML с загруженной страницы
	$xmlPageXPath = new DOMXPath($xmlPageDom);	// Создание нового объекта XPath DOM
	return $xmlPageXPath;	// Возврат объекта XPath
}

// Функция для создания запроса GET с использованием cURL
function curlGet($url) {
	$ch = curl_init();	// Инициализация сеанса cURL
	// Настройка параметров cURL
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url);
	$results = curl_exec($ch);	// Выполнение сеанса cURL
	curl_close($ch);	// Закрытие сессии cURL
	return $results;	// Вернуть результаты
}

// Функция для очистки содержимого между двумя строками
function scrapeBetween($item, $start, $end){
	$item = stristr($item, $start);
	$item = substr($item, strlen($start));
	$stop = stripos($item, $end);
	$data = substr($item, 0, $stop);
	return $data;
}

// Функция для создания нескольких асинхронных запросов curl
function curlMulti($urls) {
	$mh = curl_multi_init();	// Инициализация мультисессии cURL
	// Для каждого из URL в массиве
	foreach ($urls as $id => $d) {
		$ch[$id] = curl_init();	// Инициализация сеанса cURL
		$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
		curl_setopt($ch[$id], CURLOPT_URL, $url);
		curl_setopt($ch[$id], CURLOPT_RETURNTRANSFER, TRUE);
		curl_multi_add_handle($mh, $ch[$id]);	// Добавление сеансов cURL в мультисессию cURL
	}
	$running = NULL;	// Установка $running в NULL
	do {
		curl_multi_exec($mh, $running);	// Параллельное выполнение мультисессии cURL
	} while ($running > 0);	// Пока $running больше нуля
	// За каждую сессию cURL
	foreach($ch as $id => $content) {
		$results[$id] = curl_multi_getcontent($content);	// Добавить результаты в массив $results
		curl_multi_remove_handle($mh, $content);	// Удалить мультисессию cURL
	}
	curl_multi_close($mh);	// Закрытие мультисессии cURL
	return $results;	// Вернуть массив результатов
}

$booksPageUrl = 'https://www.bookvoed.ru/books?q=php';	// Присвоение URL страницы  книг для работы с ней

$booksPageSrc = curlGet($booksPageUrl);	// Запросить страницу с книгами															

$pageCount = scrapeBetween($booksPageSrc, '<span class="Xy">1</span> из ', ' </div>');	// Считывание количества страниц

$booksPageUrl = NULL;

for ($i = 0; $i < $pageCount; $i++) {
	$offset = 60 * $i;
	$booksPageUrl = 'https://www.bookvoed.ru/books?q=php&offset='.$offset.'&_part=books';	// Присвоение URL страницы  книг для работы с ней
	$booksPageSrc = curlGet($booksPageUrl);	// Запросить страницу с книгами
	$booksPageXPath = returnXPathObject($booksPageSrc);	// Создание нового объекта XPath DOM
	$booksPagesUrls = $booksPageXPath->query('//a[@class="Xd ee"]/@href');	// Запрос атрибутов книг
	
	// Проверка на существование
	if ($booksPagesUrls->length > 0) {
		// Для каждого URL страницы книги
		for ($j = 0; $j < $booksPagesUrls->length; $j++) {
			$booksUrls[] = $booksPagesUrls->item($j)->nodeValue;	// Добавление URL в массив
			//echo $booksUrls[$j+$offset]."<br>";
		}
	}
	$booksPageUrl = NULL;	// Обнуление строки $booksPageUrl
	$booksPageXPath = NULL;	// Обнуление объекта $booksPageXPath
	$booksPagesUrls = NULL;	// Обнуление объекта $booksPagesUrls
	sleep(rand(1, 2));	// Пауза
}

$uniquebooksUrls = array_values(array_unique($booksUrls));	// Удаление дубликатов из массива и переиндексация

$bookPages = curlMulti($uniquebooksUrls);	// Вызов функции curlMulti и передача массива URL

$dbUser = 'root';	// Имя пользователя базы данных
$dbPass = '1234';	// Пароль базы данных
$dbHost = 'localhost'; // Хост базы данных
$dbName = 'books';	// Имя базы данных
$tableName = 'books'; // Имя таблицы

// Попытка создания нового соединения с базой данных
try {
	$cxn = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName, $dbUser, $dbPass);	// Создаем соединение	
	$cxn->exec("set names utf8");	// Настройка кодировки
	$cxn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	// Изменение режима ошибки по умолчанию с PDO::ERRMODE_SILENT на PDO::ERRMODE_EXCEPTION
	$cxn->exec("ALTER TABLE ".$dbName." AUTO_INCREMENT=0;");
} catch(PDOException $e) {
	echo 'Error: ' . $e->getMessage();	// Показать ошибку исключения
}

$keys = implode(",", array_keys($mapping));
// Для каждой страницы книги
foreach ($bookPages as $bookPage) {
	
	$bookPageXPath = returnXPathObject($bookPage);	// Создание нового объекта XPath DOM
	$h1s = $bookPageXPath->query('//h1');	// Запрос на заголовок
	$h1 = NULL;
	if ($h1s -> length > 0) {
		$h1 = $h1s->item(0)->nodeValue;	// Получаем значение 1-го заголовка
	}

	$bookInfo = array();	// Массив всех данных по одной книге
	$bookInfo[":title"] = utf8_decode($h1);	// Занесли заголовок
	
	echo "<h1>".$bookInfo[":title"]."</h1>"; // Вывести заголовок
	
	// Запросы на данные таблицы
	$tws = $bookPageXPath->query('//td[@class="tw"]');
	$uws = $bookPageXPath->query('//td[@class="uw"]');
	// Массивы с требуемыми данными
	$tw = array();
	$uw = array();
	// Проверка на коректность данных 
	if ($tws -> length == $tws -> length && $tws -> length > 0) {
		for ($j = 0; $j < $tws->length; $j++) {
			$tw[] = $tws->item($j)->nodeValue;
			$uw[] = $uws->item($j)->nodeValue;
		}
	}
	//echo "<h2>" .$tws -> length . " " . $uws -> length . "</h2>";	// Вывод числа строк в обоих столбцах
	
	//	Занесение полученных данных из таблицы в $bookInfo в соответсвии с ключами из $mapping
	for ($j = 0; $j < count($tw); $j++) {
		$key = utf8_decode($tw[$j]);
		$value = utf8_decode($uw[$j]);
		$book_info_key = array_search($key, $mapping);
		$bookInfo[":".$book_info_key] = $value;
	}
	
	// Обнуление объектов для повторного использования
	$bookPageXPath = NULL;
	//$title = NULL;
	//$h1s = NULL;
	//$tw= NULL;
	//$uw= NULL;
	
	// Нужно получить SQL запрос:
	// insert into books (`id`,`title`,`series`,`publisher`,`year`,`pages`,`binding`,`ISBN`,`dimensions`,`format`,`code`,`inbase`,`authors`,`subject`,`circulation`,`author`,`translator`) VALUES ('','Создаем динамические веб-, Робин','','Питер','2016','768','мягкий','978-5-4461-0825-1','16,50 см x 23,20 см','232.00mm x 165.00mm x 37.00mm','1273758','ПИТЕР Никсон Создаем динамические веб-сайты с помощью PHP,mySOL,JavaScript,CSS и HTML 4-е изд','Никсон, Робин','Для разработчиков','','','Вильчинский, Н. ');
	
	// Подготовка списка значений заголовков таблиц для sql запроса
	$value_array = array();
	foreach ($mapping as $key => $value){
		if(array_key_exists(":".$key, $bookInfo)){
			$value_array[":".$key] = $bookInfo[":".$key];	// При наличии данных заносим значения в $value_array по нужным ключам
		}
		else{
			$value_array[":".$key] = NULL;	// Если ключ не существует в $bookInfo для совпадающего ключаб, то $value_array заносим NULL
		}
	}
		
	vardump($value_array);
	$values = implode(",", array_keys($value_array));
	
	//vardump($values);

	// Проверка на совпадение заголовков
	$query = "select id from $tableName where title = :title_in;";	// :title - слот(шаблон) в которое будет подставлено новое значение
	$selectEbook = $cxn->prepare($query); // Подготовка к выполнению sql запроса
	$selectEbook->execute(array(":title_in" => $value_array[":title"]));	// отправление sql запроса с подставлением данных
	// var_dump($selectEbook);
	$isHas = false;
	foreach ($selectEbook as $row) {	// $selectEbook - все найденые id (массив); $row - текущее значение элемента массива - перебераемая строка
		$isHas = true;	// В цикл не зайдет если нет данных
	}

	// Вызов SQL запроса для не существующих заголовков
	if (!$isHas) {
		$query = "insert into $tableName ($keys) VALUES ($values);";
		//echo $query;
		var_dump($values);
		$insertEbook = $cxn->prepare($query);
		$insertEbook->execute($value_array);
	}
	else{
		$updateData = "";	// Строка для запроса sql на обновление
		foreach($mapping as $k_m => $v_m){
			if($k_m != "title" && $k_m != "id"){
				$updateData.= $k_m." = :".$k_m.","; // Формаирование sql строки
			}
		}
		$updateData = substr($updateData, 0, -1);	// Удаление 1-го символа с сконца
		
		$query = "UPDATE $tableName SET $updateData WHERE title = :title;";	// Обновление данных если данные уже существуют
		$updateEbook = $cxn->prepare($query);	// Подготовка запроса
		$updateEbook->execute($value_array);	// Выполнение запроса | :title от $value_array подставляется на место :title в строке запроса $query
	}	
}
?>