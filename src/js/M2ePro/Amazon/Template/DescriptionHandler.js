AmazonTemplateDescriptionHandler = Class.create();
AmazonTemplateDescriptionHandler.prototype = Object.extend(new CommonHandler(), {

    //----------------------------------

    initialize: function()
    {
        // todo duplicate

        this.setValidationCheckRepetitionValue('M2ePro-description-tpl-title',
                                                M2ePro.text.title_not_unique_error,
                                                'Template_Description', 'title', 'id',
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

    preview_click: function()
    {
        this.submitForm(M2ePro.url.preview, true);
    },

    //----------------------------------

    attribute_sets_confirm: function()
    {
        var self = AmazonTemplateDescriptionHandlerObj;

        AttributeSetHandlerObj.confirmAttributeSets();
    }

    //----------------------------------
});