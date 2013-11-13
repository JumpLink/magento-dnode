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
  private $category;
  private $attribute_set;
  private $product_attribute;

  public function __construct() {
    $this->product  = new JumpLink_API_Model_Product_Api;
    $this->customer = new Mage_Customer_Model_Customer_Api_V2;
    $this->category = new JumpLink_API_Model_Category_Api;
    $this->attribute_set = new JumpLink_API_Model_Product_Attribute_Set_Api;
    $this->product_attribute = new JumpLink_API_Model_Product_Attribute_Api;
  }

  protected $red   = "\033[0;31m";
  protected $blue  = "\033[0;34m";
  protected $reset = "\033[0m";

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
  public function product_items($filters = null, $store = null, callable $cb)
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
  public function product_info($productId, $store = null, $attributes = null, $identifierType = null, callable $cb)
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
  public function product_infos($productIds, $store = null, $attributes = null, $identifierType = null, callable $cb)
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
  public function product_items_info($filters = null, $store = null, $attributes = null, callable $cb)
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
  public function product_items_info_2($filters = null, $store = null, callable $cb)
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
  public function product_items_all($store = null, callable $cb)
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
     $cb($this->product->export());
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
  public function product_create($type, $set, $sku, $productData, $store = null, callable $cb)
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
  public function product_update($productId, $productData, $store = null, $identifierType = null, callable $cb)
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
  public function product_setSpecialPrice($productId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null, $identifierType = null, callable $cb)
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
  public function product_getSpecialPrice($productId, $store = null, callable $cb)
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
  public function customer_create($customerData, callable $cb)
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
  public function customer_info($customerId, $attributes = null, callable $cb)
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
  public function customer_items($filters, $store, callable $cb)
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
  public function customer_update($customerId, $customerData, callable $cb)
  {
    $cb($this->customer->update($customerId, $customerData));
  }

  /**
   * Delete customer
   *
   * @param int $customerId
   * @return boolean
   */
  public function customer_delete($customerId, callable $cb)
  {
    $cb($this->customer->delete($customerId));
  }


  /**
   * Retrieve category data
   *
   * @param int $categoryId
   * @param string|int $store
   * @param array $attributes
   * @return array
   */
  public function category_info($categoryId, $store = null, $attributes = null, callable $cb)
  {
    $cb($this->category->info($categoryId, $store, $attributes));
  }

  /**
   * Create new category
   *
   * @param int $parentId
   * @param array $categoryData
   * @return int
   */
  public function category_create($parentId, $categoryData, $store = null, callable $cb)
  {
    $cb($this->category->create($parentId, $categoryData, $store));
  }

  /**
   * Update category data
   *
   * @param int $categoryId
   * @param array $categoryData
   * @param string|int $store
   * @return boolean
   */
  public function category_update($categoryId, $categoryData, $store = null, callable $cb)
  {
    $cb($this->category->update($categoryId, $categoryData, $store));
  }

  /**
   * Retrieve level of categories for category/store view/website
   *
   * @param string|int|null $website
   * @param string|int|null $store
   * @param int|null $categoryId
   * @return array
   */
  public function category_level($website = null, $store = null, $categoryId = null, callable $cb)
  {
    $cb($this->category->level($website, $store, $categoryId));
  }

  /**
   * Retrieve category tree
   *
   * @param int $parent
   * @param string|int $store
   * @return array
   */
  public function category_tree($parentId = null, $store = null, callable $cb)
  {
    $cb($this->category->tree($parentId, $store));
  }

  /**
   * Move category in tree
   *
   * @param int $categoryId
   * @param int $parentId
   * @param int $afterId
   * @return boolean
   */
  public function category_move($categoryId, $parentId, $afterId = null, callable $cb)
  {
    $cb($this->category->move($categoryId, $parentId, $afterId));
  }

  /**
   * Delete category
   *
   * @param int $categoryId
   * @return boolean
   */
  public function category_delete($categoryId, callable $cb)
  {
    $cb($this->category->delete($categoryId));
  }

  /**
   * Retrieve list of assigned products to category
   *
   * @param int $categoryId
   * @param string|int $store
   * @return array
   */
  public function category_assignedProducts($categoryId, $store = null, callable $cb)
  {
    $cb($this->category->assignedProducts($categoryId, $store));
  }

  /**
   * Assign product to category
   *
   * @param int $categoryId
   * @param int $productId
   * @param int $position
   * @return boolean
   */
  public function category_assignProduct($categoryId, $productId, $position = null, $identifierType = null, callable $cb)
  {
    $cb($this->category->assignProduct($categoryId, $productId, $position, $identifierType));
  }

  /**
   * Update product assignment
   *
   * @param int $categoryId
   * @param int $productId
   * @param int $position
   * @return boolean
   */
  public function category_updateProduct($categoryId, $productId, $position = null, $identifierType = null, callable $cb)
  {
    $cb($this->category->updateProduct($categoryId, $productId, $position, $identifierType));
  }


  /**
   * Remove product assignment from category
   *
   * @param int $categoryId
   * @param int $productId
   * @return boolean
   */
  public function category_removeProduct($categoryId, $productId, $identifierType = null, callable $cb)
  {
    $cb($this->category->removeProduct($categoryId, $productId, $identifierType));
  }

  /**
   * Retrieve attribute set list
   *
   * @return array
   */
  public function attributeset_items(callable $cb)
  {
    $cb($this->attribute_set->items());
  }

  /**
   * Retrieve attribute set list with info
   *
   * @return array
   */
  public function attributeset_items_info(callable $cb)
  {
    $cb($this->attribute_set->items_info());
  }

  /**
   * Retrieve attribute set list with full information about attribute with list of options
   *
   * @return array
   */
  public function attributeset_export(callable $cb)
  {
    $cb($this->attribute_set->export());
  }

  /**
   * Create new attribute set based on another set
   *
   * @param string $attributeSetName
   * @param string $skeletonSetId
   * @return integer
   */
  public function attributeset_create($attributeSetName, $skeletonSetId, callable $cb)
  {
    $cb($this->attribute_set->create($attributeSetName, $skeletonSetId));
  }

  /**
   * Remove attribute set
   *
   * @param string $attributeSetId
   * @param bool $forceProductsRemove
   * @return bool
   */
  public function attributeset_remove($attributeSetId, $forceProductsRemove = false, callable $cb)
  {
    $cb($this->attribute_set->remove($attributeSetId, $forceProductsRemove));
  }

  /**
   * Add attribute to attribute set
   *
   * @param string $attributeId
   * @param string $attributeSetId
   * @param string|null $attributeGroupId
   * @param string $sortOrder
   * @return bool
   */
  public function attributeset_attributeAdd($attributeId, $attributeSetId, $attributeGroupId = null, $sortOrder = '0', callable $cb)
  {
    $cb($this->attribute_set->attributeAdd($attributeId, $attributeSetId, $attributeGroupId, $sortOrder));
  }

  /**
   * Remove attribute from attribute set
   *
   * @param string $attributeId
   * @param string $attributeSetId
   * @return bool
   */
  public function attributeset_attributeRemove($attributeId, $attributeSetId, callable $cb)
  {
    $cb($this->attribute_set->attributeRemove($attributeId, $attributeSetId));
  }

  /**
   * Create group within existing attribute set
   *
   * @param  string|int $attributeSetId
   * @param  string $groupName
   * @return int
   */
  public function attributeset_groupAdd($attributeSetId, $groupName, callable $cb)
  {
    $cb($this->attribute_set->groupAdd($attributeSetId, $groupName));
  }

  /**
   * Rename existing group
   *
   * @param string|int $groupId
   * @param string $groupName
   * @return boolean
   */
  public function attributeset_groupRename($groupId, $groupName, callable $cb)
  {
    $cb($this->attribute_set->groupRename($groupId, $groupName));
  }

  /**
   * Remove group from existing attribute set
   *
   * @param  string|int $attributeGroupId
   * @return bool
   */
  public function attributeset_groupRemove($attributeGroupId, callable $cb)
  {
    $cb($this->attribute_set->groupRemove($attributeGroupId));
  }

  /**
   * Retrieve attributes from specified attribute set
   *
   * @param int $setId
   * @return array
   */
  public function productattribute_items($setId, callable $cb)
  {
    $cb($this->product_attribute->items($setId));
  }

  /**
   * Retrieve attributes from specified attribute set with full information about attribute with list of options
   *
   * @param int $setId
   * @return array
   */
  public function productattribute_items_info($setId, callable $cb)
  {
    $cb($this->product_attribute->items_info($setId));
  }

  /**
   * Get full information about attribute with list of options
   *
   * @param integer|string $attribute attribute ID or code
   * @return array
   */
  public function productattribute_info($attribute, callable $cb)
  {
    $cb($this->product_attribute->info($attribute));
  }

  /**
   * Retrieve attribute options
   *
   * @param int $attributeId
   * @param string|int $store
   * @return array
   */
  public function productattribute_options($attributeId, $store = null, callable $cb)
  {
    $cb($this->product_attribute->options($attributeId, $store = null));
  }

  /**
   * Retrieve list of possible attribute types
   *
   * @return array
   */
  public function productattribute_types(callable $cb)
  {
    $cb($this->product_attribute->types());
  }

  /**
   * Create new product attribute
   *
   * @param array $data input data
   * @return integer
   */
  public function productattribute_create($data, callable $cb)
  {
    $cb($this->product_attribute->create($data));
  }

  /**
   * Update product attribute
   *
   * @param string|integer $attribute attribute code or ID
   * @param array $data
   * @return boolean
   */
  public function productattribute_update($attribute, $data, callable $cb)
  {
    $cb($this->product_attribute->update($attribute, $data));
  }

  /**
   * Remove attribute
   *
   * @param integer|string $attribute attribute ID or code
   * @return boolean
   */
  public function productattribute_remove($attribute, callable $cb)
  {
    $cb($this->product_attribute->remove($attribute));
  }

  /**
   * Add option to select or multiselect attribute
   *
   * @param  integer|string $attribute attribute ID or code
   * @param  array $data
   * @return bool
   */
  public function productattribute_addOption($attribute, $data, callable $cb)
  {
    $cb($this->product_attribute->addOption($attribute, $data));
  }

  /**
   * Remove option from select or multiselect attribute
   *
   * @param  integer|string $attribute attribute ID or code
   * @param  integer $optionId option to remove ID
   * @return bool
   */
  public function productattribute_removeOption($attribute, $optionId, callable $cb)
  {
    $cb($this->product_attribute->removeOption($attribute, $optionId));
  }

}

