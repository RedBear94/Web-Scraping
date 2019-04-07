<?php

class Scrape {
	// Объявление переменных и массивов классов
	public $url;
	public $source;
	public $baseUrl;
	private $parsedUrl = array();
	
	// Метод Construct, вызываемый при создании объекта
	function __construct($url) {
		$this->url = $url;	// Настройка атрибута URL
		$this->source = $this->curlGet($this->url);
		$this->xPathObj = $this->returnXpathObject($this->source);
		$this->parsedUrl = parse_url($this->url);
		$this->baseUrl = $this->parsedUrl['scheme'].'://'.$this->parsedUrl['host'];
	}
	
	// Способ создания запроса GET с использованием cURL
	public function curlGet($url) {
		$ch = curl_init();	// Инициализация сеанса cURL
		// Настройка параметров cURL
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	// Возврат передачи в виде строки
		curl_setopt($ch, CURLOPT_URL, $url);	// Настройка URL
		$results = curl_exec($ch);	// Выполнение сеанса cURL
		curl_close($ch);	// Закрытие сессии cURL
		return $results;	// Вернуть результаты
	}
	
	// Метод для возврата объекта XPath
	public function returnXPathObject($item) {
		$xmlPageDom = new DomDocument();	// Создание нового объекта DomDocument
		@$xmlPageDom->loadHTML($item);	// Загрузка HTML с скачанной страницы
		$xmlPageXPath = new DOMXPath($xmlPageDom);	// Создание нового объекта XPath DOM
		return $xmlPageXPath;	// Возврат объекта XPath
	}
}
?>