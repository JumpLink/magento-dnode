var dnode = require('dnode');

var server = dnode(function (remote, conn) {
    this.welcome_message = function (messsage, answer) {
      console.log(messsage);
      answer("Hello Magento, you are welcome!");
      console.log(remote);
      remote.product_items(null,null, function (result) {
        console.log(result);
      });
    };
});
server.listen(6060);