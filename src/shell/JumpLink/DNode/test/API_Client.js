var dnode = require('dnode');

var test_customer_items_filter = function () {

  dnode.connect(6060, function (remote, conn) {
      var options = {
        where : {
          'filter': [
            {
              "key": "customer_id",
              "value":"469"
            },
          ]
        }
      }
      var store = null; 
      remote.customer_items(options.where, store, function (result) {
          console.log(result);
          console.log("result length: "+result.length);
          conn.end();
      });
  });

}

var test_customer_items_complex_filter = function () {

  dnode.connect(6060, function (remote, conn) {
      var options = {
        where : {
          'complex_filter': [
            {
              "key": "customer_id",
              "value": [
                {
                  "key": "eq",
                  "value": "469"
                }
              ]
            },
          ]
        }
      }
      var store = null; 
      remote.customer_items(options.where, store, function (result) {
          console.log(result);
          console.log("result length: "+result.length);
          conn.end();
      });
  });

}

var test_product_items_info = function () {

  dnode.connect(6060, function (remote, conn) {
      var options = {
        where : {
          'filter': [
            {
              "key": "sku",
              "value":"211-413-401"
            },
          ]
        }
      }
      var filters = null;
      var store = null;
      var attributes = null;
      var identifierType = null;

      remote.product_items_info(filters, store, attributes, identifierType,  function (result) {
          console.log(result);
          console.log("result length: "+result.length);
          conn.end();
      });
  });

}


var test_product_items_info_filter = function () {

  dnode.connect(6060, function (remote, conn) {
      var options = {
        where : {
          'filter': [
            {
              "key": "sku",
              "value":"211-413-401"
            },
          ]
        }
      }
      var store = null;
      var attributes = null;

      remote.product_items_info(options.where, store, attributes,  function (result) {
          console.log(result);
          //console.log("result length: "+result.length);
          conn.end();
      });
  });
}

var test_product_items_filter_from_to = function () {
  dnode.connect(6060, function (remote, conn) {
      var options = {
        where : {
          complex_filter: [
            {
              key: "product_id",
              value: 
                {
                  "key": "from",
                  "value": 50
                }
            },
            {
              key: "product_id",
              value: 
                {
                  key: "to",
                  value: 60
                }
            },
          ]
        }
      }
      var store = null;
      remote.product_items(options.where, store, function (result) {
          console.log(result);
          console.log("result length: "+result.length);
          conn.end();
      });
  });
}

var test_product_items_info_filter_from_to = function () {
  dnode.connect(6060, function (remote, conn) {
      var options = {
        where : {
          complex_filter: [
            {
              key: "product_id",
              value: 
                {
                  key: "from",
                  value: 5
                }
            },
            {
              key: "product_id",
              value: 
                {
                  key: "to",
                  value: 10
                }
            },
          ]
        }
      }
      var store = null;
      var attributes = null;
      remote.product_items_info(options.where, store, attributes, function (result) {
          console.log(result);
          console.log("result length: "+result.length);
          conn.end();
      });
  });
}

var test_product_infos = function () {
  dnode.connect(6060, function (remote, conn) {
      var productIds = ["211-413-401", "AC601019B"];
      var store = null;
      var attributes = null;
      var identifierType = "sku";
      remote.product_infos(productIds, store, attributes, identifierType, function (result) {
          console.log(result);
          console.log("result length: "+result.length);
          conn.end();
      });
  });
}

//test_customer_items_filter();
//test_customer_items_complex_filter();
//test_product_items_info();
//test_product_items_info_filter();
test_product_items_filter_from_to();
//test_product_items_info_filter_from_to();
//test_product_infos();
