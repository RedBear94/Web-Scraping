<?php

// Функция отправки формы с использованием метода CURL POST
function curlPost($postUrl, $postFields) {
	
	//$useragent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36';	// Настройка пользовательского агента браузера
	$useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36 OPR/56.0.3051.116 (Edition Yx)';
	$cookie = 'cookie.txt';	// Настройка файла cookie для хранения файлов cookie
	
	$ch = curl_init();	// Инициализация сессии cURL

	// Настройка параметров cURL
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	// Запретить cURL проверять сертификат SSL
	curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);	// Скрипт должен терпеть неудачу при ошибке
	curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);	// Использовать куки
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);	// Следуйте за местоположением: заголовков
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	// Возврат передачи в виде строки
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);	// Настройка cookiefile
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);	// Настройка cookiejar
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);	// Настройка useragent
	curl_setopt($ch, CURLOPT_URL, $postUrl);	// Установка URL-адреса для POST
			
	curl_setopt($ch, CURLOPT_POST, TRUE);	// Метод настройки как POST
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));	// Установка полей POST в качестве массива

	$results = curl_exec($ch);	// Выполнение сеанса cURL
	curl_close($ch);	// Закрытие сессии cURL
	
	// Проверка успешного входа в систему путем проверки наличия строки
	if ($results) {
		//echo $results;
		return $results;
	} else {
		return FALSE;
	}
}

$userEmail = 'pavel3345@yandex.ru';	// Настройка адреса электронной почты для входа в сайт
$userPass = 'minato1994';	// Установка пароля для входа в сайт

// Установка URL-адреса для POST
$postUrl = 'https://passport.yandex.ru/passport?mode=embeddedauth';

// Поля ввода формы формы как 'name' => 'value'
$postFields = array(
	'login' => $userEmail, 
	'passwd' => $userPass, 
	'real_retpath' => 'https://mail.yandex.ru//?msid=1543765428.09139.122252.824642&m_pssp=domik', 
	'idkey' => '5d3b4c2ea06d10b20d8dfa96fad467a610',
	'one' => '1',
	'extended' => '1',
	'retpath' => 'https://yandex.ru',
	'source' => 'password'
);

$loggedIn = curlPost($postUrl, $postFields);	// Выполнение  curlPost входа и сохранение страницы результатов в $loggedIn
//echo $loggedIn;	// Повторение записи на странице

// Функция возврата объекта XPath
function returnXPathObject($item) {
	$xmlPageDom = new DomDocument();	// Создание нового объекта DomDocument
	@$xmlPageDom->loadHTML($item);	// Загрузка HTML с загруженной страницы
	// @ - инструктирует процедуру игнорировать любые найденные ошибки
	// это сделано, потому что бывают случаи когда HTML-файл в Интернете будет содержать недопустимую разметку
	$xmlPageXPath = new DOMXPath($xmlPageDom);	// Создание нового объекта XPath DOM
	return $xmlPageXPath;	// Возвращение объекта XPath
}
$packtPageXpath = returnXPathObject($loggedIn);	// Создание нового объекта XPath DOM
$postCount = $packtPageXpath->query('//div[@class="desk-notif-card__mail-count desk-notif-card__has-unread-mail"]');	// Запрос на число писем
$packtData = array();
if ($postCount->length > 0) {$packtData['post count'] = $postCount->item(0)->nodeValue;} else {echo 'error';}

function vardump($var) {
  echo '<pre>';
  var_dump($var);
  echo '</pre>';
}
//vardump($packtData)
echo "На яндексе число писем на вашей почте $userEmail: ".	$packtData["post count"];
//echo $postCount->item(0)->nodeValue;;
?>