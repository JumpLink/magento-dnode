<?php
/**
 * @category    JumpLink
 * @package     JumpLink_DNode
 * @copyright   Copyright (c) Pascal Garber <jumplink@gmail.com>
 * @license     MIT license
 */

/**
 * Catalog product api DNode
 *
 * @category   JumpLink
 * @package    JumpLink_DNode
 * @author     Pascal Garber <jumplink@gmail.com>
 */

ini_set('memory_limit', '-1');

class JumpLink_DNode_Model_API {
  private $product;
  private $customer;

  public function __construct() {
    $this->product = new JumpLink_API_Model_Product_Api;
    $this->customer = new Mage_Customer_Model_Customer_Api_V2;
  }
  
  /*
  **********************************************
  * Product API
  **********************************************
  */

  /**
   * Retrieve list of products with basic info (id, sku, type, set, name)
   *
   * @param array $filters
   * @param string|int $store
   * @param callback $cb array
   */
  public function product_items($filters = null, $store = null, $cb)
  {
    $cb($this->product->items($filters, $store));
  }

  /**
   * Retrieve product info
   *
   * @param int|string $productId
   * @param string|int $store
   * @param stdClass $attributes
   * @param string $identifierType OPTIONAL If 'sku' - search product by SKU, if any except for NULL - search by ID,
   *                                        otherwise - try to determine identifier type automatically
   * @param callback $cb array of attributes
   */
  public function product_info($productId, $store = null, $attributes = null, $identifierType = null, $cb)
  {
    $cb($this->product->info($productId, $store, $attributes, $identifierType));
  }

  /**
   * Retrieve array of product info's for given sku's or product_id's
   *
   * @param array of int|string $productIds
   * @param string|int $store
   * @param stdClass $attributes
   * @param string $identifierType OPTIONAL If 'sku' - search product by SKU, if any except for NULL - search by ID,
   *                                        otherwise - try to determine identifier type automatically
   * @param callback $cb array of products of array of attributes
   */
  public function product_infos($productIds, $store = null, $attributes = null, $identifierType = null, $cb)
  {
    $result = array();
    $length = count($productIds);
    print ("productIds: ".$productIds."\n");
    print ("length: ".$length."\n");
    for ($i=0; $i < count($productIds); $i++) {
      $this->product_info($productIds[$i], $store, $attributes, $identifierType, function($product_info) use ($cb, &$result, $length, $i) {
        $result[] = $product_info;
        print ("result[".$i."][product_id]: ".$result[$i]["product_id"]."\n");
         print ("length: ".count($result)."\n");
        if($i >= $length - 1) {
          return $cb($result);
        }
      });
    }
  }

  /**
   * Retrieve list of products with much more info
   *
   * @param array $filters
   * @param string|int $store
   * @param stdClass $attributes
   * @param callback $cb array
   */
  public function product_items_info($filters = null, $store = null, $attributes = null, $cb)
  {
    $this->check_filter($filters);
    $this->product_items($filters, $store, function($products) use ($cb) {
      $length = count($products);
      print("length ".$length);
      for ($i=0; $i < count($products); $i++) { 
        print("product->product_id): ".$products[$i]['product_id']."\n");
        $this->product_info($products[$i]['product_id'], $store, $attributes, "product_id", function($product_info) use (&$products, $i, $length, $cb) {
          $products[$i] = $product_info;
          print($products[$i]);
          if($i >= $length - 1)
            return $cb($products);
        });
      }
    });
  }

  /**
   * Retrieve list of products with much more info
   *
   * @param array $filters
   * @param string|int $store
   * @param callback $cb array
   */
  public function product_items_info_2($filters = null, $store = null, $cb)
  {
    $cb($this->product->items_info($productId, $store));
  }

  /**
   * Retrieve list of products with much more info
   *
   * @param array $filters
   * @param string|int $store
   * @param callback $cb array
   */
  public function product_items_all($store = null, $cb)
  {
    $cb($this->product->items_all($store));
  }

  /**
   * Retrieve list of products with much more info using the ImportExport Module
   *
   * @param array $filters
   * @param string|int $store
   * @param callback $row callback for each row
   * @param callback $cb callback on finish
   */
  public function product_export(callable $cb)
  {
    $cb("load", array());
    $product_export = new Mage_ImportExport_Model_Export_Entity_Product;
    $callback_writer = new JumpLink_ImportExport_Model_Export_Adapter_Callback;
    $callback_writer->setCallback($cb);
    $product_export->setWriter($callback_writer);
    print("product_export\n");
    $cb("start", array());
    $product_export->export();
    //print($result);*/
    $cb("finish", array());
  }


  /**
   * Create new product.
   *
   * @param string $type
   * @param int $set
   * @param string $sku
   * @param array $productData
   * @param string $store
   * @param callback $cb int
   */
  public function product_create($type, $set, $sku, $productData, $store = null, $cb)
  {
    $cb($this->product->create($type, $set, $sku, $productData, $store));
  }

  /**
   * Update product data
   *
   * @param int|string $productId
   * @param array $productData
   * @param string|int $store
   * @param callback boolean
   */
  public function product_update($productId, $productData, $store = null, $identifierType = null, $cb)
  {
    $cb($this->product->update($productId, $productData, $store, $identifierType));
  }

  /**
   * Update product special price
   *
   * @param int|string $productId
   * @param float $specialPrice
   * @param string $fromDate
   * @param string $toDate
   * @param string|int $store
   * @param string $identifierType OPTIONAL If 'sku' - search product by SKU, if any except for NULL - search by ID,
   *                                        otherwise - try to determine identifier type automatically
   * @param callback boolean
   */
  public function product_setSpecialPrice($productId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null, $identifierType = null, $cb)
  {
    $cb($this->product->setSpecialPrice($productId, $specialPrice, $fromDate, $toDate, $store, $identifierType));
  }

  /**
   * Retrieve product special price
   *
   * @param int|string $productId
   * @param string|int $store
   * @param callback array
   */
  public function product_getSpecialPrice($productId, $store = null, $cb)
  {
    $cb($this->product->getSpecialPrice($productId, $store));
  }


  /*
  **********************************************
  * Customer API
  **********************************************
  */

  /**
   * Create new customer
   *
   * @param array $customerData
   * @return int
   */
  public function customer_create($customerData, $cb)
  {
    $cb($this->customer->create($customerData));
  }

  /**
   * Retrieve customer data
   *
   * @param int $customerId
   * @param array $attributes
   * @return array
   */
  public function customer_info($customerId, $attributes = null, $cb)
  {
    $cb($this->customer->info($customerId, $attributes));
  }

    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     *
     * @param array $filters
     * @param string|int $store
     * @return array
     */
  public function customer_items($filters, $store, $cb)
  {
    print_r ($this->blue."customer_items".$this->reset."\n");
    $this->check_filter($filters);

    $cb($this->customer->items($filters, $store));
  }

  /**
   * Update customer data
   *
   * @param int $customerId
   * @param array $customerData
   * @return boolean
   */
  public function customer_update($customerId, $customerData, $cb)
  {
    $cb($this->customer->update($customerId, $customerData));
  }

  /**
   * Delete customer
   *
   * @param int $customerId
   * @return boolean
   */
  public function customer_delete($customerId, $cb)
  {
    $cb($this->customer->delete($customerId));
  }

}

