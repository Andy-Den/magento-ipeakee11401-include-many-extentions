function GetItemID() {
    var path = window.location.href;
    var itemID = "";
    var count = 0;
    for (i = 1; i < path.length; i++) {
        if (count != 12) {
            if ((path[i] == '0') || (path[i] == '1') || (path[i] == '2') || (path[i] == '3') || (path[i] == '4') || (path[i] == '5') || (path[i] == '6') || (path[i] == '7') || (path[i] == '8') || (path[i] == '9')) {
                itemID += path[i];
                count++;
            }
            else {
                if (count != 12) {
                    itemID = "";
                    count = 0;
                }
            }
        }
    }
    return itemID;
}
function AddToCart() {
    var cart = "http://cart.payments.ebay.com.au/sc/add?item=iid:"+GetItemID()+",qty:1&ssPageName=CART:ATC";
    window.top.location.href = cart;
}
function BuyNow() {
    var cart = "http://offer.ebay.com.au/ws/eBayISAPI.dll?BinConfirm&rev=1&item=" + GetItemID();
    window.top.location.href = cart;
}