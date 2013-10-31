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

class JumpLink_DNode_Model_API {
  private $product;
  private $customer; 

  private $red   = "\033[0;31m";
  private $blue  = "\033[0;34m";
  private $reset = "\033[0m";

  public function __construct() {
    $this->product = new Mage_Catalog_Model_Product_Api_V2;
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
   * @param callback $cb array
   */
  public function product_info($productId, $store = null, $attributes = null, $identifierType = null, $cb)
  {
    $cb($this->product->info($productId, $store, $attributes, $identifierType));
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
    print_r ($this->blue.json_encode($filters).$this->reset."\n");

    if (isset($filters->filter)) {
      print_r ($this->blue."filters->filter is set ".$filters->filter.$this->reset."\n");
      foreach ($filters->filter as $_filter) {
        if (!isset($_filter->key))
          print_r ($this->red."filter->key not set ".$_filter->key.$this->reset."\n");
        else
          print_r ($this->blue."filter->key is set ".$_filter->key.$this->reset."\n");
        if (!isset($_filter->value))
          print_r ($this->red."filter->value not set ".$_filter->value.$this->reset."\n");
        else
          print_r ($this->blue."filter->value is set ".$_filter->value.$this->reset."\n");
      }
    }else {
      print_r ($this->red."filters->filter not set".$filters->filter.$this->reset."\n");
    }

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

