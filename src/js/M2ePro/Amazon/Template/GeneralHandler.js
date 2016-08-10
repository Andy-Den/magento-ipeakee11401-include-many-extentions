AmazonTemplateGeneralHandler = Class.create();
AmazonTemplateGeneralHandler.prototype = Object.extend(new CommonHandler(), {

    //----------------------------------

    initialize: function()
    {
        this.setValidationCheckRepetitionValue('M2ePro-listing-tpl-title',
                                                M2ePro.text.title_not_unique_error,
                                                'Template_General', 'title', 'id',
                                                M2ePro.formData.id);
    },

    //----------------------------------

    duplicate_click: function($headId)
    {
        var attrSetEl = $('attribute_sets_fake');

        if (attrSetEl) {
            $('attribute_sets').remove();
            attrSetEl.observe('change', AttributeSetHandlerObj.changeAttributeSets);
            attrSetEl.id = 'attribute_sets';
            attrSetEl.name = 'attribute_sets[]';
            attrSetEl.addClassName('M2ePro-validate-attribute-sets');

            AttributeSetHandlerObj.confirmAttributeSets();
        }

        if ($('attribute_sets_breadcrumb')) {
            $('attribute_sets_breadcrumb').remove();
        }
        $('attribute_sets_container').show();
        $('attribute_sets_buttons_container').show();

        CommonHandlerObj.duplicate_click($headId);
    },

    //----------------------------------

    attribute_sets_confirm: function()
    {
        var self = AmazonTemplateGeneralHandlerObj;

        AttributeSetHandlerObj.confirmAttributeSets();

        // show filds wich depends on attributes set
        $$('.requirie-attribute-set').invoke('show');
    },

    //----------------------------------

    account_id_change: function()
    {
        var self = AmazonTemplateGeneralHandlerObj;
        var accountId = this.value;

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
    }

    //----------------------------------
});