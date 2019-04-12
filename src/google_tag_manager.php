<?php

use GoogleTagManager\DatalayerGenerator;

/**
 * Obecna trida pro predavani zprav do dataLayer pro GTM.
 * Pro kazdy obchod by se mela vytvorit nova trida, ktera dedi GoogleTagManager, ktera bude upravovat nektera specifika, napriklad praci s produkty.
 *
 * Pouziti
 * =======
 * Vytvoreni vlastni tridy
 * ```
 * class MyEshopTagManager extends GoogleTagManager {
 * }
 * ```
 *
 * Pak nekde v ApplicationController::_before_filter()
 * ```
 * $this->gtm = MyEshopTagManager::GetInstance()
 * ```
 *
 */
class GoogleTagManager {

	private static $Instance = null;
	var $dataLayer = array();
	var $dataLayerObject = array();

	var $controller = null;
	var $ecommerce_promotion_impressions = [];
	var $ecommerce_measurements = [];
	var $ecommerce_checkout = [];
	var $ecommerce_additional_objects = [];

	private function __construct() { }
	private function __clone() { }
	private function __wakeup() { }


	/**
	 *
	 *
	 * @param array $options
	 * @todo upravit. pokud uz je instance k dispozici, rovnou ji vratit
	 */
	static function &GetInstance($controller=null, $options=array()) {
		$options += array();

		if (!defined("GOOGLE_TAG_MANAGER_ID")) {
			$null = null;
			return $null;
		}

		if (!isset(self::$Instance)) {
			self::$Instance = new static();
		}
		if ($controller) {
			self::$Instance->controller = $controller;
		}
		self::$Instance->options = $options;
		if (defined("GOOGLE_TAG_MANAGER_ID") && $controller) {
			$controller->tpl_data["GOOGLE_TAG_MANAGER_ID"] = GOOGLE_TAG_MANAGER_ID;
			$controller->tpl_data["gtm"] = self::$Instance;
		}

		return self::$Instance;
	}

	/**
	 * Prida do fronty merenych udalosti dalsi hodnoty.
	 *
	 * Vetsinou se uklada vse do jedne fronty. Opakovane pouziti setObjectParams() tedy stale uklada hodnoty do jedne fronty.
	 * Pro nektera mereni je treba vytvorit novou frontu.
	 * Pouzitim close_queue v $options se fronta na konci volani setObjectParams() uzavre a pristi zapis bude probihat do nove fronty.
	 */
	function setObjectParams($values=array(),$options=array()) {
		$options += array(
			"close_queue" => false,
			"key" => null,
		);

		if (isset($options["key"])) {
			if (!array_key_exists($options["key"], $this->dataLayer)) {
				$this->dataLayer[$options["key"]] = array();
			}
			$this->dataLayer[$options["key"]] = array_merge($this->dataLayer[$options["key"]],$values);
		} else {
			$this->dataLayerObject = array_merge($this->dataLayerObject, $values);
		}

		if ($options["close_queue"]) {
			$this->closeQueue();
		}
	}

	function closeQueue($params=array()) {
		$params += array(
			"key" => null,
		);
		if ($this->dataLayerObject) {
			$this->dataLayer[] = $this->dataLayerObject;
			$this->dataLayerObject = array();
		}
	}

	/**
	 * Vrati vsechny zpravy jako pole.
	 * Pred tim uzavre posledni docasnou frontu a vrati vse.
	 *
	 * @param array $options
	 * - format - vystupni format zprav
	 * 	-raw: standardni php array
	 * 	-json
	 * @return array
	 */
	function getDataLayerMessages($options=[]) {
		$options += [
			"format" => "raw",
		];
		$this->closeQueue();

		$_dl = $this->dataLayer;

		if ($_obj = $this->getBasketCheckout()) {
			$_dl[] = [
				"event" => "checkout",
				"ecommerce" => [
					"checkout" => [
						"actionFields" => [
							"step" => 1,
						],
					],
					"products" => $_obj,
				],
			];
		}
		if ($_obj = $this->getPromotionView()) {
			$promotionParts = $this->_splitObject($_obj);
			foreach($promotionParts as $_pp) {
				$_dl[] = [
					"ecommerce" => [
						"promoView" => [
							"promotions" => $_pp,
						],
					],
					"event" => "promotionView",
				];
			}
		}
		if ($measurements = $this->getMeasurements()) {
			foreach($measurements as $_ip) {
				$_dl[] = $_ip;
			}
		}

		foreach($this->ecommerce_additional_objects as $_obj) {
			if ($_msg = $_obj->getDataLayerMessage()) {
				$_dl[] = $_msg;
			}
		}

		if ($options["format"] == "json") {
			$_dl = array_map('json_encode', $_dl);
		}
		return $_dl;
	}

	/**
	 * Rozdeleni jednoho objektu na nekolik mensich.
	 * Payload, zprava odeslana do GA muze byt dlouha max. 8kB,
	 * proto je treba nektere objekty rozdelit
	 * napr. ecommerce["impressions"] muze obsahovat mnoho produktu a jejich odeslani do GA se nepodari.
	 */
	protected function _splitObject($object) {
		$payload_length_limit = 7500;
		/** Toto pole se pripoji k testovanemu objektu jako rezerva pri pocitani delky payloadu
		 * Sestava z nazvu parametru a hodnoty (vymyslene, s nejakym obsahem v bezne delce)
		 */
		$payload_additional_fields = [
			"tid" => "UA-12345678-10",
			"cid" => "01234567890123456789",
			"cg1" => "Fake contentGroup1 field",
			"cg2" => "Fake contentGroup2 field",
			"cg3" => "Fake contentGroup3 field",
			"cg4" => "Fake contentGroup4 field",
			"cg5" => "Fake contentGroup5 field",
			"cd1" => "Fake customDimension1 field",
			"cd2" => "Fake customDimension2 field",
			"cd3" => "Fake customDimension3 field",
			"cd4" => "Fake customDimension4 field",
			"cd5" => "Fake customDimension5 field",
			"dt" => "Fake page title",
			"sr" => "1920x1080",
			"vp" => "1100x960",
			"sd" => "24-bit",
			"ni" => "1",
			"ul" => "en-us",
			"dl" => ATK14_HTTP_HOST,
			"ec" => "Ecommerce",
			"ea" => "Promo View",
			"de" => "UTF-8",
			"v" => "1",
			"a" => "0123456789",
			"jid" => "0123456789",
			"gjid" => "0123456789",
		];
		if (mb_strlen(json_encode($object)) < $payload_length_limit ) {
			return [$object];
		}
		$_splittedObjects = [];

		$_objectPart = [];
		while($_element = array_shift($object)) {
			$testAr = array_merge($_objectPart, $_element, $payload_additional_fields);

			if (mb_strlen(json_encode($testAr)) > $payload_length_limit ) {
				$_splittedObjects[] = $_objectPart;
				$_objectPart = [];
			}
			$_objectPart[] = $_element;
		}
		$_splittedObjects[] = $_objectPart;
		return $_splittedObjects;
	}

	function getDataLayerMessagesJson() {
		return $this->getDataLayerMessages(["format" => "json"]);
	}

	/**
	 * Alias to getDataLayerMessages.
	 *
	 * @internal for backward compatibility
	 */
	function getDataLayer($options=[]) {
		return $this->getDataLayerMessages($options);
	}

	/**
	 * Vrati data, ktera se budou odesilat pri kliknuti na odkaz produktu.
	 *
	 * Data budou rozdelena do skupin podle elementu, ve kterych jsou produkty seskupene.
	 * Elementy jsou uvedeny pomoci css selectoru.
	 *
	 * Jen pro elementy (Generatory), ktere maji v options uvedeny list_selector, aby bylo mozne najit v kodu odpovidajici seznam produktu
	 */
	function getProductsData($options=[]) {
		$options += [
			"format" => "raw",
		];
		$products_data = [];
		$ids_map = [];
		foreach($this->ecommerce_measurements as $i) {
			if (!isset($i->options["list_selector"])) {
				continue;
			}
			if ($products = $i->getDataLayerMessage(["key" => "url", "return_list" => false])) {
				$_pd = [
					"dataLayer" => $products,
				];
				$i->options["add_to_cart_selector"] && ($_pd["add_to_cart_selector"] = $i->options["add_to_cart_selector"]);
				$i->options["list_selector"] && ($_pd["selector"] = $i->options["list_selector"]);
				$i->options["list"] && ($_pd["list_name"] = $i->options["list"]);
				$products_data[] = $_pd;
				$ids_map += $i->getIdsMap();
			}
		}
		$products_data["ids_map"] = $ids_map;
		if ($options["format"] == "json") {
			return json_encode($products_data);
		}
		return $products_data;
	}

	function getProductsDataJson() {
		return $this->getProductsData(["format" => "json"]);
	}

	protected function getMeasurements() {
		if (!$this->ecommerce_measurements) {
			return null;
		}

		$_ecommerce_messages = [];
		foreach($this->ecommerce_measurements as $i) {
			if ($products = $i->getDataLayerMessage()) {
				$activity = $i->getActivity();
				$_ecommerce_messages[] = [
					"ecommerce" => [
						"${activity}" => $products,
					],
				];
			}
		}
		return $_ecommerce_messages;
	}

	protected function getBasketCheckout() {
		if (!$this->ecommerce_checkout) {
			return null;
		}
		$_checkoutMsg =  [];
		foreach($this->ecommerce_checkout as $i) {
			if ($_message = $i->getDataLayerMessage()) {
				$_checkoutMsg = array_merge($_checkoutMsg, $_message);
			}
		}
		return $_checkoutMsg;
	}

	protected function getPromotionView() {
		if (!$this->ecommerce_promotion_impressions) {
			return null;
		}
		$_ec = [];
		foreach($this->ecommerce_promotion_impressions as $p) {
			$_ec[] = $p->getDataLayerMessage();
		}
		return $_ec;
	}

	function &getCurrentQueue($options=array()) {
		$options += array(
			"key" => null,
		);
		$null = null;
		if (isset($options["key"])) {
			if (!array_key_exists($options["key"], $this->dataLayer)) {
				return $this->dataLayer[$options["key"]];
			} else {
				return $null;
			}
		} else {
			return $this->dataLayerObject;
		}
	}

	/**
	 * Zjisti aktualni uri
	 */
	function getUri() {
		if (is_null($this->controller)) {
			return "";
		}
		return $this->controller->request->getUri();
	}


	function measurePromotionImpression(DatalayerGenerator $banner) {
		$this->ecommerce_promotion_impressions[] = $banner;
	}

	function measureProductImpressions(DatalayerGenerator $impression_object) {
		$this->ecommerce_measurements[] = $impression_object;
	}

	function measureProductDetail(DatalayerGenerator $product_detail_object) {
		$this->ecommerce_measurements[] = $product_detail_object;
	}

	function measureCheckout(DatalayerGenerator $basket) {
		$this->ecommerce_checkout[] = $basket;
	}

	function measureOtherObject(DatalayerGenerator $object) {
		$this->ecommerce_additional_objects[] = $object;
	}
}
