<?php
/**
 * This allows to inject some functionality into base class.
 *
 * Just create new class in your application which extends ItemConverter class
 *
 * ```
 * namespace DatalayerGenerator\MessageGenerators\GA4\ItemConverter;
 *
 * class DatalayerGenerator\MessageGenerators\GA4\ItemConverter\ItemConverterBase extends ItemConverter {
 *   function __construct() {
 *     var_dump("ItemConverter injected");
 *   }
 * }
 * ```
 */
namespace DatalayerGenerator\MessageGenerators\GA4\ItemConverter;
use DatalayerGenerator\MessageGenerators\GA4\EventBase;

if (!class_exists("ItemConverterBase")) {
	class ItemConverterBase extends ItemConverter{
	};
}
