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
  protected $store;
  protected $product;
  protected $customer;
  protected $category;
  protected $attribute_set;
  protected $product_attribute;

  public function __construct() {
    $this->store = new JumpLink_API_Model_Store_Api;
    $this->product  = new JumpLink_API_Model_Product_Api;
    $this->customer = new Mage_Customer_Model_Customer_Api_V2;
    $this->category = new JumpLink_API_Model_Category_Api;
    $this->attribute_set = new JumpLink_API_Model_Product_Attribute_Set_Api;
    $this->product_attribute = new JumpLink_API_Model_Product_Attribute_Api;
  }

  protected $red   = "\033[0;31m";
  protected $blue  = "\033[0;34m";
  protected $reset = "\033[0m";


  /**
   * Handle Errors
   * Statuscodes:
   *   404: Not found
   *   500: Server Error
   *
   * @param string $message
   * @param callback $cb array
   */
  protected function handle_error(callable $cb, $message) {
    print($message."\n");
    switch ($message) {
      case 'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away':
        die;
      break;
      case 'product_not_exists':
      case 'store_not_exists':
        $cb(array('status' => 404, 'message' => $message)); 
      break;
      default:
        $cb(array('status' => 500, 'message' => $message));
      break;
    }
  }

  public function call_dynamic($object) {
    if(method_exists($this, $object->method)) {
      switch ($object->method) {
        case 'store_tree':
          return $this->store_tree(null);
          break;
        case 'store_items':
          return $this->store_items(null);
          break;
        case 'store_info':
          return $this->store_info(null, $object->storeId);
          break;
        case 'product_items':
          return $this->product_items(null, $object->filters, $object->store);
          break;
        case 'product_info':
          return $this->product_info(null, $object->productId, $object->store, $object->attributes, $object->identifierType);
          break;
        case 'product_infos':
          return $this->product_infos(null, $object->productIds, $object->store, $object->attributes, $object->identifierType);
          break;
        case 'product_items_info':
          return $this->product_items_info(null, $object->filters, $object->store, $object->attributes);
          break;
        case 'product_items_info_2':
          return $this->product_items_info_2(null, $object->filters, $object->store);
          break;
        case 'product_items_all':
          return $this->product_items_all(null, $object->store);
          break;
        case 'product_export':
          return $this->product_export(null, $object->productId, $object->store, $object->all_stores, $object->attributes, $object->identifierType, $object->integrate_set, $object->normalize);
          break;
        case 'product_create':
          return $this->product_create(null, $object->type, $object->set, $object->sku, $object->productData, $object->store);
          break;
        case 'product_update':
          return $this->product_update(null, $object->productId, $object->productData, $object->store, $object->identifierType );
          break;
        case 'product_setSpecialPrice':
          return $this->product_setSpecialPrice(null, $object->productId, $object->specialPrice, $object->fromDate, $object->toDate, $object->store, $object->identifierType);
          break;
        case 'product_getSpecialPrice':
          return $this->product_getSpecialPrice(null, $object->productId, $object->store);
          break;
        case 'customer_create':
          return $this->customer_create(null, $object->customerData);
          break;
        case 'customer_info':
          return $this->customer_info(null, $object->customerId, $object->attributes);
          break;
        case 'customer_items':
          return $this->customer_items(null, $object->filters, $object->store);
          break;
        case 'customer_update':
          return $this->customer_update(null, $object->customerId, $object->customerData);
          break;
        case 'customer_delete':
          return $this->customer_delete(null, $object->customerId);
          break;
        case 'category_info':
          return $this->category_info(null, $object->categoryId, $object->store, $object->attributes);
          break;
        case 'category_create':
          return $this->category_create(null, $object->parentId, $object->categoryData, $object->store);
          break;
        case 'category_update':
          return $this->category_update(null, $object->categoryId, $object->categoryData, $object->store );
          break;
        case 'category_level':
          return $this->category_level(null, $object->website, $object->store, $object->categoryId);
          break;
        case 'category_tree':
          return $this->category_tree(null, $object->parentId, $object->store);
          break;
        case 'category_move':
          return $this->category_move(null, $object->categoryId, $object->parentId, $object->afterId);
          break;
        case 'category_delete':
          return $this->category_delete(null, $object->categoryId);
          break;
        case 'category_assignedProducts':
          return $this->category_assignedProducts(null, $object->categoryId, $object->store);
          break;
        case 'category_assignProduct':
          return $this->category_assignProduct(null, $object->categoryId, $object->productId, $object->position, $object->identifierType);
          break;
        case 'category_updateProduct':
          return $this->category_updateProduct(null, $object->categoryId, $object->productId, $object->position, $object->identifierType);
          break;
        case 'category_removeProduct':
          return $this->category_removeProduct(null, $object->categoryId, $object->productId, $object->identifierType);
          break;
        case 'attributeset_info':
          return $this->attributeset_info(null, $object->setId);
          break;
        case 'attributeset_items':
          return $this->attributeset_items(null);
          break;
        case 'attributeset_items_info':
          return $this->attributeset_items_info(null);
          break;
        case 'attributeset_export':
          return $this->attributeset_export(null, $object->setId);
          break;
        case 'attributeset_create':
          return $this->attributeset_create(null, $object->attributeSetName, $object->skeletonSetId);
          break;
        case 'attributeset_remove':
          return $this->attributeset_remove(null, $object->attributeSetId, $object->forceProductsRemove);
          break;
        case 'attributeset_attributeAdd':
          return $this->attributeset_attributeAdd(null, $object->attributeId, $object->attributeSetId, $object->attributeGroupId, $object->sortOrder);
          break;
        case 'attributeset_attributeRemove':
          return $this->attributeset_attributeRemove(null, $object->attributeId, $object->attributeSetId);
          break;
        case 'attributeset_groupAdd':
          return $this->attributeset_groupAdd(null, $object->attributeSetId, $object->groupName);
          break;
        case 'attributeset_groupRename':
          return $this->attributeset_groupRename(null, $object->groupId, $object->groupName);
          break;
        case 'attributeset_groupRemove':
          return $this->attributeset_groupRemove(null, $object->attributeGroupId);
          break;
        case 'productattribute_all':
          return $this->productattribute_all(null);
          break;
        case 'productattribute_items':
          return $this->productattribute_items(null, $object->setId);
          break;
        case 'productattribute_items_info':
          return $this->productattribute_items_info(null, $object->setId);
          break;
        case 'productattribute_info':
          return $this->productattribute_options(null, $object->attribute);
          break;
        case 'productattribute_options':
          return $this->productattribute_options(null, $object->attribute, $object->store);
          break;
        case 'productattribute_types':
          return $this->productattribute_types(null, $object->data);
          break;
        case 'productattribute_create':
          return $this->productattribute_create(null);
          break;
        case 'productattribute_update':
          return $this->productattribute_update(null, $object->attribute, $object->data);
          break;
        case 'productattribute_remove':
          return $this->productattribute_remove(null, $object->attribute);
          break;
        case 'productattribute_addOption':
          return $this->productattribute_addOption(null, $object->attribute, $object->data);
          break;
        case 'productattribute_removeOption':
          return $this->productattribute_removeOption(null, $object->attribute, $object->optionId);
          break;

        default:
          return '{"error": "method '.$object->method.' not implemented"}';
          break;
      }
    } else {
      print('{"error": "method '.$object->method.' not exists"}');
    }
  }

  public function check_filter($filters) {
    print_r ($this->blue.json_encode($filters).$this->reset."\n");

    if (isset($filters->filter)) {
      print_r ($this->blue."filters->filter is set: ".$filters->filter.$this->reset."\n");
      foreach ($filters->filter as $_filter) {
        if (!isset($_filter->key)) {
          print_r ($this->red."filter->key not set ".$this->reset."\n");
          return "filter->key not set";
        }
        else
          print_r ($this->blue."filter->key is set: ".$_filter->key.$this->reset."\n");
        if (!isset($_filter->value)) {
          print_r ($this->red."filter->value not set ".$this->reset."\n");
          return "filter->value not set";
        }
        else
          print_r ($this->blue."filter->value is set: ".$_filter->value.$this->reset."\n");
      }
    } else if(isset($filters->complex_filter)){
      print_r ($this->blue."filters->complex_filter is set: ".$filters->complex_filter.$this->reset."\n");
    } else {
      print_r ($this->red."filters->filter or filters->complex_filter not set".$this->reset."\n");
      return "filters->complex_filter not set";
    }
  }

  /*
  **********************************************
  * Store API
  **********************************************
  */

  /**
   * Retrieve stores list
   *
   * @param callable $cb(array) callback
   */
  public function store_tree(callable $cb)
  {
    try {
      $result = $this->store->tree();
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve stores list
   *
   * @param callable $cb(array) callback
   */
  public function store_items(callable $cb)
  {
    try {
      $result = $this->store->items();
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve store data
   *
   * @param callable $cb(array) callback
   * @param string|int $storeId
   */
  public function store_info(callable $cb, $storeId)
  {
    try {
      $result = $this->store->info($storeId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }
  
  /*
  **********************************************
  * Product API
  **********************************************
  */

  /**
   * Retrieve list of products with basic info (id, sku, type, set, name)
   *
   * @param callable $cb callback
   * @param array $filters
   * @param string|int $store
   */
  public function product_items(callable $cb, $filters = null, $store = null)
  {
    try {
      $result = $this->product->items($filters, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve product info
   *
   * @param callable $cb(array) callback
   * @param int|string $productId
   * @param string|int $store
   * @param stdClass $attributes
   * @param string $identifierType OPTIONAL If 'sku' - search product by SKU, if any except for NULL - search by ID,
   *                                        otherwise - try to determine identifier type automatically
   * @param callback $cb array of attributes
   */
  public function product_info(callable $cb, $productId, $store = null, $attributes = null, $identifierType = null)
  {
    try {
      $result = $this->product->info($productId, $store, $attributes, $identifierType);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve array of product info's for given sku's or product_id's
   *
   * @param callable $cb(array) callback
   * @param array of int|string $productIds
   * @param string|int $store
   * @param stdClass $attributes
   * @param string $identifierType OPTIONAL If 'sku' - search product by SKU, if any except for NULL - search by ID,
   *                                        otherwise - try to determine identifier type automatically
   * @param callback $cb array of products of array of attributes
   */
  public function product_infos(callable $cb, $productIds, $store = null, $attributes = null, $identifierType = null)
  {
    try {
      $result = array();
      $length = count($productIds);
      // print ("productIds: ".$productIds."\n");
      // print ("length: ".$length."\n");
      for ($i=0; $i < count($productIds); $i++) {
        $this->product_info(function($product_info) use ($cb, &$result, $length, $i) {
          $result[] = $product_info;
          // print ("result[".$i."][product_id]: ".$result[$i]["product_id"]."\n");
          // print ("length: ".count($result)."\n");
          if($i >= $length - 1) {
            break;
          }
        }, $productIds[$i], $store, $attributes, $identifierType);
      }
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve list of products with much more info
   *
   * @param callable $cb(array) callback
   * @param array $filters
   * @param string|int $store
   * @param stdClass $attributes
   */
  public function product_items_info(callable $cb, $filters = null, $store = null, $attributes = null)
  {

    try {
      $this->check_filter($filters);
      $this->product_items(function($products) use ($cb) {
        $length = count($products);
        // print("length ".$length);
        for ($i=0; $i < count($products); $i++) { 
          // print("product->product_id): ".$products[$i]['product_id']."\n");
          $this->product_info(function($product_info) use (&$products, $i, $length, $cb) {
            $products[$i] = $product_info;
            // print($products[$i]);
            if($i >= $length - 1)
              break;
          }, $products[$i]['product_id'], $store, $attributes, "product_id");
        }
      }, $filters, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve list of products with much more info
   *
   * @param callable $cb(array) callback
   * @param array $filters
   * @param string|int $store
   */
  public function product_items_info_2(callable $cb, $filters = null, $store = null)
  {
    try {
      $result = $this->product->items_info($productId, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve list of products with much more info
   *
   * @param callable $cb(array) callback
   * @param array $filters
   * @param string|int $store
   */
  public function product_items_all(callable $cb, $store = null)
  {
    try {
      $result = $this->product->items_all($store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve list of products with much more info using the ImportExport Module
   *
   * @param callable $cb(array) callback
   * @param array $filters
   * @param string|int $store
   * @param callback $row callback for each row
   */
  public function product_export(callable $cb, $productId=null, $store = null, $all_stores=true, $attributes = null, $identifierType = null, $integrate_set = false, $normalize = true)
  {
    try {
      $result = $this->product->export($productId, $store, $all_stores, $attributes, $identifierType, $integrate_set, $normalize);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }


  /**
   * Create new product.
   *
   * @param callable $cb(integer) callback
   * @param string $type
   * @param int $set
   * @param string $sku
   * @param array $productData
   * @param string $store
   */
  public function product_create(callable $cb, $type, $set, $sku, $productData, $store = null)
  {
    try {
      $result = $this->product->create($type, $set, $sku, $productData, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Update product data
   *
   * @param callable $cb(boolean) callback
   * @param int|string $productId
   * @param array $productData
   * @param string|int $store
   */
  public function product_update(callable $cb, $productId, $productData, $store = null, $identifierType = null)
  {
    try {
      $result = $this->product->update($productId, $productData, $store, $identifierType);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Update product special price
   *
   * @param callable $cb(boolean) callback
   * @param int|string $productId
   * @param float $specialPrice
   * @param string $fromDate
   * @param string $toDate
   * @param string|int $store
   * @param string $identifierType OPTIONAL If 'sku' - search product by SKU, if any except for NULL - search by ID,
   *                                        otherwise - try to determine identifier type automatically
   */
  public function product_setSpecialPrice(callable $cb, $productId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null, $identifierType = null)
  {
    try {
      $result = $this->product->setSpecialPrice($productId, $specialPrice, $fromDate, $toDate, $store, $identifierType);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve product special price
   *
   * @param callable $cb(array) callback
   * @param int|string $productId
   * @param string|int $store
   */
  public function product_getSpecialPrice(callable $cb, $productId, $store = null)
  {
    try {
      $result = $this->product->getSpecialPrice($productId, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }


  /*
  **********************************************
  * Customer API
  **********************************************
  */

  /**
   * Create new customer
   *
   * @param callable $cb(integer) callback
   * @param array $customerData
   */
  public function customer_create(callable $cb, $customerData)
  {
    try {
      $result = $this->customer->create($customerData);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve customer data
   *
   * @param callable $cb(array) callback
   * @param int $customerId
   * @param array $attributes
   */
  public function customer_info(callable $cb, $customerId, $attributes = null)
  {
    try {
      $result = $this->customer->info($customerId, $attributes);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve list of products with basic info (id, sku, type, set, name)
   *
   * @param callable $cb(array) callback
   * @param array $filters
   * @param string|int $store
   */
  public function customer_items(callable $cb, $filters, $store)
  {
    //print_r ($this->blue."customer_items".$this->reset."\n");
    $this->check_filter($filters);
    try {
      $result = $this->customer->items($filters, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Update customer data
   *
   * @param callable $cb(boolean) callback
   * @param int $customerId
   * @param array $customerData
   */
  public function customer_update(callable $cb, $customerId, $customerData)
  {
    try {
      $result = $this->customer->update($customerId, $customerData);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Delete customer
   *
   * @param callable $cb(boolean) callback
   * @param int $customerId
   */
  public function customer_delete(callable $cb, $customerId)
  {
    try {
      $result = $this->customer->delete($customerId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }


  /**
   * Retrieve category data
   *
   * @param callable $cb(array) callback
   * @param int $categoryId
   * @param string|int $store
   * @param array $attributes
   */
  public function category_info(callable $cb, $categoryId, $store = null, $attributes = null)
  {
    try {
      $result = $this->category->info($categoryId, $store, $attributes);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Create new category
   *
   * @param callable $cb(integer) callback
   * @param int $parentId
   * @param array $categoryData
   */
  public function category_create(callable $cb, $parentId, $categoryData, $store = null)
  {
    try {
      $result = $this->category->create($parentId, $categoryData, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Update category data
   *
   * @param callable $cb(boolean) callback
   * @param int $categoryId
   * @param array $categoryData
   * @param string|int $store
   */
  public function category_update(callable $cb, $categoryId, $categoryData, $store = null)
  {
    try {
      $result = $this->category->update($categoryId, $categoryData, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve level of categories for category/store view/website
   *
   * @param callable $cb(array) callback
   * @param string|int|null $website
   * @param string|int|null $store
   * @param int|null $categoryId
   */
  public function category_level(callable $cb, $website = null, $store = null, $categoryId = null)
  {
    try {
      $result = $this->category->level($website, $store, $categoryId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve category tree
   *
   * @param callable $cb(array) callback
   * @param int $parent
   * @param string|int $store
   */
  public function category_tree(callable $cb, $parentId = null, $store = null)
  {
    try {
      $result = $this->category->tree($parentId, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Move category in tree
   *
   * @param callable $cb(boolean) callback
   * @param int $categoryId
   * @param int $parentId
   * @param int $afterId
   */
  public function category_move(callable $cb, $categoryId, $parentId, $afterId = null)
  {
    try {
      $result = $this->category->move($categoryId, $parentId, $afterId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Delete category
   *
   * @param callable $cb(boolean) callback
   * @param int $categoryId
   */
  public function category_delete(callable $cb, $categoryId)
  {
    try {
      $result = $this->category->delete($categoryId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve list of assigned products to category
   *
   * @param callable $cb(array) callback
   * @param int $categoryId
   * @param string|int $store
   */
  public function category_assignedProducts(callable $cb, $categoryId, $store = null)
  {
    try {
      $result = $this->category->assignedProducts($categoryId, $store);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Assign product to category
   *
   * @param callable $cb(boolean) callback
   * @param int $categoryId
   * @param int $productId
   * @param int $position
   */
  public function category_assignProduct(callable $cb, $categoryId, $productId, $position = null, $identifierType = null)
  {
    try {
      $result = $this->category->assignProduct($categoryId, $productId, $position, $identifierType);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Update product assignment
   *
   * @param callable $cb(boolean) callback
   * @param int $categoryId
   * @param int $productId
   * @param int $position
   */
  public function category_updateProduct(callable $cb, $categoryId, $productId, $position = null, $identifierType = null)
  {
    try {
      $result = $this->category->updateProduct($categoryId, $productId, $position, $identifierType);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }


  /**
   * Remove product assignment from category
   *
   * @param callable $cb(boolean) callback
   * @param int $categoryId
   * @param int $productId
   */
  public function category_removeProduct(callable $cb, $categoryId, $productId, $identifierType = null)
  {
    try {
      $result = $this->category->removeProduct($categoryId, $productId, $identifierType);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve attribute set info
   *
   * @param callable $cb(array) callback
   * @param int $setId
   */
  public function attributeset_info(callable $cb, $setId)
  {
    try {
      $result = $this->attribute_set->info($setId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve attribute set list
   *
   * @param callable $cb(array) callback
   */
  public function attributeset_items(callable $cb)
  {
    try {
      $result = $this->attribute_set->items();
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve attribute set list with info
   *
   * @param callable $cb(array) callback
   */
  public function attributeset_items_info(callable $cb)
  {
    try {
      $result = $this->attribute_set->items_info();
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve a list of or one attribute set with full information about attribute with list of options
   *
   * @param callable $cb(array) callback
   * @param int $setId
   */
  public function attributeset_export(callable $cb, $setId = null)
  {
    try {
      $result = $this->attribute_set->export($setId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Create new attribute set based on another set
   *
   * @param callable $cb(integer) callback
   * @param string $attributeSetName
   * @param string $skeletonSetId
   */
  public function attributeset_create(callable $cb, $attributeSetName, $skeletonSetId)
  {
    try {
      $result = $this->attribute_set->create($attributeSetName, $skeletonSetId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Remove attribute set
   *
   * @param callable $cb(bool) callback
   * @param string $attributeSetId
   * @param bool $forceProductsRemove
   */
  public function attributeset_remove(callable $cb, $attributeSetId, $forceProductsRemove = false)
  {
    try {
      $result = $this->attribute_set->remove($attributeSetId, $forceProductsRemove);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Add attribute to attribute set
   *
   * @param callable $cb(bool) callback
   * @param string $attributeId
   * @param string $attributeSetId
   * @param string|null $attributeGroupId
   * @param string $sortOrder
   */
  public function attributeset_attributeAdd(callable $cb, $attributeId, $attributeSetId, $attributeGroupId = null, $sortOrder = '0')
  {
    try {
      $result = $this->attribute_set->attributeAdd($attributeId, $attributeSetId, $attributeGroupId, $sortOrder);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Remove attribute from attribute set
   *
   * @param callable $cb(bool) callback
   * @param string $attributeId
   * @param string $attributeSetId
   */
  public function attributeset_attributeRemove(callable $cb, $attributeId, $attributeSetId)
  {
    try {
      $result = $this->attribute_set->attributeRemove($attributeId, $attributeSetId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Create group within existing attribute set
   *
   * @param callable $cb(int) callback
   * @param string|int $attributeSetId
   * @param string $groupName
   */
  public function attributeset_groupAdd(callable $cb, $attributeSetId, $groupName)
  {
    try {
      $result = $this->attribute_set->groupAdd($attributeSetId, $groupName);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Rename existing group
   *
   * @param callable $cb(boolean) callback
   * @param string|int $groupId
   * @param string $groupName
   */
  public function attributeset_groupRename(callable $cb, $groupId, $groupName)
  {
    try {
      $result = $this->attribute_set->groupRename($groupId, $groupName);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Remove group from existing attribute set
   *
   * @param callable $cb(boolean) callback
   * @param string|int $attributeGroupId
   */
  public function attributeset_groupRemove(callable $cb, $attributeGroupId)
  {
    try {
      $result = $this->attribute_set->groupRemove($attributeGroupId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Get full list of all avaible attributes in all attributesets
   *
   * @param callable $cb(array) callback
   */
  public function productattribute_all(callable $cb)
  {
    try {
      $result = $this->product_attribute->all();
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve attributes from specified attribute set
   *
   * @param callable $cb(array) callback
   * @param int $setId
   */
  public function productattribute_items(callable $cb, $setId)
  {
    try {
      $result = $this->product_attribute->items($setId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve attributes from specified attribute set with full information about attribute with list of options
   *
   * @param callable $cb(array) callback
   * @param int $setId
   */
  public function productattribute_items_info(callable $cb, $setId)
  {
    try {
      $result = $this->product_attribute->items_info($setId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Get full information about attribute with list of options
   *
   * @param callable $cb(array) callback
   * @param integer|string $attribute attribute ID or code
   */
  public function productattribute_info(callable $cb, $attribute)
  {
    try {
      $result = $this->product_attribute->info($attribute);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve attribute options
   *
   * @param callable $cb(array) callback
   * @param int $attributeId
   * @param string|int $store
   */
  public function productattribute_options(callable $cb, $attributeId, $store = null)
  {
    try {
      $result = $this->product_attribute->options($attributeId, $store = null);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Retrieve list of possible attribute types
   *
   * @param callable $cb(array) callback
   */
  public function productattribute_types(callable $cb)
  {
    try {
      $result = $this->product_attribute->types();
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Create new product attribute
   *
   * @param callable $cb(integer) callback
   * @param array $data input data
   */
  public function productattribute_create(callable $cb, $data)
  {
    try {
      $result = $this->product_attribute->create($data);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Update product attribute
   *
   * @param callable $cb(boolean) callback
   * @param string|integer $attribute attribute code or ID
   * @param array $data
   */
  public function productattribute_update(callable $cb, $attribute, $data)
  {
    try {
      $result = $this->product_attribute->update($attribute, $data);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Remove attribute
   *
   * @param callable $cb(boolean) callback
   * @param integer|string $attribute attribute ID or code
   */
  public function productattribute_remove(callable $cb, $attribute)
  {
    try {
      $result = $$this->product_attribute->remove($attribute);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Add option to select or multiselect attribute
   *
   * @param callable $cb(boolean) callback
   * @param integer|string $attribute attribute ID or code
   * @param array $data
   */
  public function productattribute_addOption(callable $cb, $attribute, $data)
  {
    try {
      $result = $this->product_attribute->addOption($attribute, $data);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

  /**
   * Remove option from select or multiselect attribute
   *
   * @param callable $cb(boolean) callback
   * @param integer|string $attribute attribute ID or code
   * @param integer $optionId option to remove ID
   */
  public function productattribute_removeOption(callable $cb, $attribute, $optionId)
  {
    try {
      $result = $this->product_attribute->removeOption($attribute, $optionId);
    } catch (Exception $e) {
      $this->handle_error($cb, $e->getMessage());
      return $e->getMessage();
    }
    if($cb != null && is_callable($cb))
      $cb($result);
    else
      return $result;
  }

}

