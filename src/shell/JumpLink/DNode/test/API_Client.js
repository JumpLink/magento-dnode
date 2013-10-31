var dnode = require('dnode');

var test_customer_info_filter = function () {

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

var test_customer_info_complex_filter = function () {

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


//test_customer_info_filter();
test_customer_info_complex_filter();

/*params = array('complex_filter'=>
    array(
        array('key'=>'created_at','value'=>array('key' =>'from','value' => '2013-05-03 01:01:01')),
        array('key'=>'customer_id','value'=>array('key' =>'eq','value' => 3)),

    ),

);*/