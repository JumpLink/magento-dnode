var dnode = require('dnode');

var server = dnode(function (remote, conn) {
    this.dispatchEvent = function (eventName, args, cb) {
      console.log(eventName);
      console.log(args);
      cb("thx");
/*      remote.product_items(null,null, function (result) {
        console.log(result);
      });*/
    };
});
server.listen(7070);