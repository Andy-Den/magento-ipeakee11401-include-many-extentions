AmazonListingChannelSettingsHandler = Class.create();
AmazonListingChannelSettingsHandler.prototype = Object.extend(new CommonHandler(), {

    //----------------------------------

    initialize: function() {},

    //----------------------------------

    account_id_change: function()
    {
        var self = AmazonListingChannelSettingsHandlerObj;
        var accountId = this.value;

        self.hideEmptyOption($('account_id'));

        new Ajax.Request(M2ePro.url.getMarketplacesForAccount,
        {
            method: 'post',
            asynchronous : false,
            parameters : {
                account_id : accountId
            },
            onSuccess: function (transport)
            {
                var data = transport.responseText.evalJSON(true);

                var html = '<option class="empty"></option>';
                data.each(function(v) {
                    html += '<option value="' + v.code + '">' + v.label + '</option>\n';
                });

                $('marketplace_id').update(html);
                $('marketplace_id').value = M2ePro.formData.marketplace_id;
                $('marketplace_id_container').show();
            }
        });
    },

    //----------------------------------

    marketplace_id_change: function()
    {
        AmazonListingChannelSettingsHandlerObj.hideEmptyOption($('marketplace_id'));
    },

    //----------------------------------

    sku_mode_change: function()
    {
        var self = AmazonListingChannelSettingsHandlerObj;

        if (this.value == self.SKU_MODE_CUSTOM_ATTRIBUTE) {
            $('sku_custom_attribute_container').show();
        } else {
            $('sku_custom_attribute_container').hide();
        }
    },

    sku_custom_attribute_change: function()
    {
        AmazonListingChannelSettingsHandlerObj.hideEmptyOption($('sku_custom_attribute'));
    },

    //----------------------------------

    general_id_mode_change: function()
    {
        var self = AmazonListingChannelSettingsHandlerObj;

        if (this.value == self.GENERAL_ID_MODE_CUSTOM_ATTRIBUTE) {
            $('general_id_custom_attribute_container').show();
        } else {
            $('general_id_custom_attribute_container').hide();
        }
    },

    general_id_custom_attribute_change: function()
    {
        AmazonListingChannelSettingsHandlerObj.hideEmptyOption($('general_id_custom_attribute'));
    }

    //----------------------------------
});