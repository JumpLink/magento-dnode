var dnode = require('dnode');

var red, blue, reset;
red   = '\033[31m';
blue  = '\033[34m';
reset = '\033[0m';

var server = dnode(function (remote, conn) {
    this.dispatchEvent = function (eventName, args, cb) {
      var lookingfor = "";

      /* stock */
      lookingfor = "stock";
      if(eventName.indexOf(lookingfor) !== -1) {
        console.log("");
        console.log(red+lookingfor+reset);
        console.log(blue+eventName+reset);
        console.log(args);
      }
      switch (eventName) {
        case "cataloginventory_stock_item_save_commit_after":
          console.log("");
          console.log("inventory changed!");
          console.log(blue+eventName+reset);
          console.log(args);
        break;
      }

      /* stock */
      lookingfor = "customer";
      if(eventName.indexOf(lookingfor) !== -1) {
        console.log("");
        console.log(red+lookingfor+reset);
        console.log(blue+eventName+reset);
        console.log(args);
      }
      switch (eventName) {
        case "customer_register_success":
          console.log("");
          console.log(blue+eventName+reset);
          console.log(args);
        break;
      }

      /* cart */
      lookingfor = "cart";
      if(eventName.indexOf(lookingfor) !== -1) {
        console.log("");
        console.log(red+lookingfor+reset);
        console.log(blue+eventName+reset);
        console.log(args);
      }
      switch (eventName) {
        case "controller_action_predispatch_checkout_cart_add":
        case "checkout_cart_add_product_complete":
        case "checkout_cart_update_item_complete":
        case "checkout_cart_product_add_after":
        case "checkout_cart_save_after":
        case "checkout_cart_product_update_after":
        case "checkout_cart_product_add_after":
        case "checkout_cart_product_add_after":
          console.log("");
          console.log("product add to card!");
          console.log(blue+eventName+reset);
          console.log(args);
        break;
        case "checkout_cart_update_items_after":
          console.log("");
          console.log("Warenkorb Update");
          console.log(blue+eventName+reset);
          console.log(args);
        break;   
      }
      cb();
    };
});
server.listen(7070);