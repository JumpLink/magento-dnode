<?php
/**
 * fork of http://magento.stackexchange.com/questions/9067/catalog-product-save-after-event-for-massaction/9068
 */
class JumpLink_DNode_Model_Observer
{

    protected function send($eventName, $args)
    {
        try {
		$loop = new React\EventLoop\StreamSelectLoop();
        	$dnode = new DNode\DNode($loop);
		$dnode->connect(7070, function($remote, $connection) use ($eventName, $args) {
          		$remote->dispatchEvent($eventName, $args, function($message = "") use ($connection) {
            		//echo ($message);
            		$connection->end();
          		});
        	});
        	$loop->run();
	} catch (Exception $e) {
        	 Mage::logException($e);
    	} finally {
        	return $this;
	}
    }

    public function detectProductAttributeChanges($observer)
    {
        $attributesData = $observer->getEvent()->getAttributesData();
        $productIds     = $observer->getEvent()->getProductIds();
        $eventName      = $observer->getEvent()->getName();
        $user           = Mage::getSingleton('admin/session')->getUser();
        $this->send($eventName, $productIds);

/*        
        foreach ($productIds as $id) {
            $change             = new array();
            $change['product_id'] = $id;
            $change['new_values'] = $attributesData;
            $change['user_id']    = ;
            $change['created']    = now();
        }

        return $this->send($eventName, $change);*/
        return $this;
    }

    public function detectProductChanges($observer)
    {
        /**
         * @var $product Mage_Catalog_Model_Product
         * @var $user    Mage_Admin_Model_User
         */
        $product    = $observer->getEvent()->getProduct();
        $eventName  = $observer->getEvent()->getName();
        $this->send($eventName, array("product_id" => $product->entity_id, "sku" => $product->sku));
        return $this;
    }
}
