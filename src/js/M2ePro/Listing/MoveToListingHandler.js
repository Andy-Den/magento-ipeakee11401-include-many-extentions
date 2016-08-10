ListingMoveToListingHandler = Class.create();
ListingMoveToListingHandler.prototype = Object.extend(new CommonHandler(), {

    //----------------------------------

    initialize: function(M2ePro)
    {
        this.M2ePro = M2ePro;
    },

    //----------------------------------

    openPopUp: function(gridHtml)
    {
        popUp = Dialog.info('', {
            draggable: true,
            resizable: true,
            closable: true,
            className: "magento",
            windowClassName: "popup-window",
            title: 'TODO TEXT',
            top: 100,
            width: 750,
            height: 500,
            zIndex: 100,
            recenterAuto: false,
            hideEffect: Element.hide,
            showEffect: Element.show
        });
        $('modal_dialog_message').style.paddingTop = '20px';
        $('modal_dialog_message').insert(gridHtml);
    },

    //----------------------------------

    getGridHtml: function(selectedProducts)
    {
        this.selectedProducts = selectedProducts;

        var self = this;
        MagentoMessageObj.clearAll();
        eval(self.M2ePro.customData.gridId + '_massactionJsObject.unselectAll()');

        new Ajax.Request(self.M2ePro.url.prepareData, {
            method: 'post',
            parameters: {
                componentMode: self.M2ePro.customData.componentMode,
                selectedProducts: Object.toJSON(self.selectedProducts)
            },
            onSuccess: function (transport) {
                if (transport.responseText == 1) {
                    alert(self.M2ePro.text.select_only_mapped_products);
                } else if (transport.responseText == 2) {
                    alert(self.M2ePro.text.select_the_same_type_products);
                } else {
                    params = transport.responseText.evalJSON();
                    new Ajax.Request(self.M2ePro.url.getGridHtml, {
                        method: 'get',
                        parameters: {
                            componentMode: self.M2ePro.customData.componentMode,
                            accountId: params.accountId,
                            marketplaceId: params.marketplaceId,
                            attrSetId: params.attrSetId,
                            ignoreListings: self.M2ePro.customData.ignoreListings
                        },
                        onSuccess: function (transport) {
                            self.openPopUp(transport.responseText);
                        }
                    });
                }
            }
        });
    },

    //----------------------------------

    submit: function(listingId)
    {
        var self = this;
        new Ajax.Request(self.M2ePro.url.moveToListing, {
            method: 'post',
            parameters: {
                componentMode: self.M2ePro.customData.componentMode,
                selectedProducts: Object.toJSON(self.selectedProducts),
                listingId: listingId
            },
            onSuccess: function (transport) {
                popUp.close();
                self.scroll_page_to_top();

                response = transport.responseText.evalJSON();

                if (response.result == 'success') {
                    eval(self.M2ePro.customData.gridId + 'JsObject.reload()');
                    MagentoMessageObj.addSuccess(self.M2ePro.text.successfully_moved);
                    return;
                }

                var message = '';
                if (response.errors == self.selectedProducts.length) { // all items failed
                    message = self.M2ePro.text.products_were_not_moved;
                } else {
                    message = self.M2ePro.text.some_products_were_not_moved;
                    eval(self.M2ePro.customData.gridId + 'JsObject.reload()');
                }

                MagentoMessageObj.addError(str_replace('%url%', self.M2ePro.url.logViewUrl, message));
            }
        });
    }

    //----------------------------------

});