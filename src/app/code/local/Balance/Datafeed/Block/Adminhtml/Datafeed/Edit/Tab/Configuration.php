<?php
class Balance_Datafeed_Block_Adminhtml_Datafeed_Edit_Tab_Configuration extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $model = Mage::getModel('datafeed/datafeed');
        $model->load($this->getRequest()->getParam('id'));
        $this->setForm($form);

        $fieldset = $form->addFieldset('datafeed_form', array('legend' => $this->__('Configuration')));

        if ($this->getRequest()->getParam('id')) {
            $fieldset->addField('feed_id', 'hidden', array(
                'name' => 'feed_id',
            ));
        }
        $fieldset->addField('cron_expr', 'hidden', array(
            'name' => 'cron_expr',
            'value' => $model->getCronExpr()
        ));
        $fieldset->addField('datafeed_category_filter', 'hidden', array(
                'name'  => 'datafeed_category_filter',
                'value' => $model->getDatafeedCategoryFilter()
        ));
        $fieldset->addField('datafeed_categories', 'hidden', array(
            'name' => 'datafeed_categories',
            'value' => $model->getFeedCategories()
        ));

        $fieldset->addField('datafeed_visibility', 'hidden', array(
            'name' => 'datafeed_visibility',
            'value' => $model->getFeedVisibility()
        ));

        $fieldset->addField('datafeed_attributes', 'hidden', array(
            'name' => 'datafeed_attributes',
            'value' => $model->getFeedAttributes()
        ));

        $fieldset->addField('datafeed_type_ids', 'hidden', array(
            'name' => 'datafeed_type_ids',
            'value' => $model->getFeedTypeIds()
        ));


        $fieldset->addField('feed_name', 'text', array(
            'label' => Mage::helper('datafeed')->__('Feed name'),
            'class' => 'required-entry refresh',
            'required' => true,
            'name' => 'feed_name',
            'id' => 'feed_name',
        ));

        $fieldset->addField('feed_type', 'select', array(
            'label' => Mage::helper('datafeed')->__('File type'),
            'required' => true,
            'class' => 'required-entry',
            'name' => 'feed_type',
            'id' => 'feed_type',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => 'xml'
                ),
                array(
                    'value' => 2,
                    'label' => 'txt'
                ),
                array(
                    'value' => 3,
                    'label' => 'csv'
                )
            )
        ));

        $fieldset->addField('feed_path', 'text', array(
            'label' => Mage::helper('datafeed')->__('Path'),
            'name' => 'feed_path',
            'required' => true,
            'value' => $model->getFeedPath()
        ));


        $fieldset->addField('feed_status', 'select', array(
            'label' => Mage::helper('datafeed')->__('File status'),
            'required' => true,
            'class' => 'required-entry',
            'name' => 'feed_status',
            'id' => 'feed_status',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => $this->__('disabled')
                ),
                array(
                    'value' => 1,
                    'label' => $this->__('enabled')
                )
            )
        ));



        $fieldset->addField('store_id', 'select', array(
            'label' => $this->__('Store View'),
            'title' => $this->__('Store View'),
            'name' => 'store_id',
            'required' => true,
            'value' => $model->getStoreId(),
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm()
        ));

        $fieldset->addField('feed_extraheader', 'textarea', array(
            'label' => Mage::helper('datafeed')->__('Extra header<br><span style="font-size:10px;">(for Amazon Data Feeds)</span>'),
            'class' => 'txt-type not-required',
            'name' => 'feed_extraheader',
            'style' => 'height:75px;width:450px;resize:vertical',
        ));


        $fieldset->addField('feed_include_header', 'select', array(
            'label' => Mage::helper('datafeed')->__('Include header'),
            'required' => true,
            'class' => 'required-entry txt-type refresh',
            'name' => 'feed_include_header',
            'id' => 'feed_include_header',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => $this->__('no')
                ),
                array(
                    'value' => 1,
                    'label' => $this->__('yes')
                )
            )
        ));
        $fieldset->addField('feed_enclose_data', 'select', array(
            'label' => Mage::helper('datafeed')->__('Enclose xml tag content inside CDATA (recommended)'),
            'required' => true,
            'class' => 'required-entry xml-type',
            'name' => 'feed_enclose_data',
            'id' => 'feed_enclose_data',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => $this->__('yes')
                ),
                array(
                    'value' => 0,
                    'label' => $this->__('no')
                )
            )
        ));

        $fieldset->addField('feed_header', 'textarea', array(
            'label' => Mage::helper('datafeed')->__('Header pattern'),
            'class' => 'refresh',
            'name' => 'feed_header',
            'required' => true,
            'style' => 'height:120px;width:500px',
        ));

        $fieldset->addField('feed_product', 'textarea', array(
            'label' => Mage::helper('datafeed')->__('Product pattern'),
            'class' => 'refresh',
            'required' => true,
            'name' => 'feed_product',
            'style' => 'height:300px;width:500px',
        ));

        $fieldset->addField('feed_footer', 'textarea', array(
            'label' => Mage::helper('datafeed')->__('Footer pattern'),
            'class' => 'xml-type refresh',
            'required' => true,
            'id' => 'feed_footer',
            'name' => 'feed_footer',
            'style' => 'height:60px;width:500px',
        ));



        $fieldset->addField('feed_separator', 'select', array(
            'label' => Mage::helper('datafeed')->__('Fields delimiter'),
            'class' => 'txt-type refresh required-entry',
            'id' => 'feed_separator',
            'required' => true,
            'name' => 'feed_separator',
            'style' => '',
            'values' => array(
                array(
                    'value' => ';',
                    'label' => ';'
                ),
                array(
                    'value' => ',',
                    'label' => ','
                ),
                array(
                    'value' => '|',
                    'label' => '|'
                ),
                array(
                    'value' => '\t',
                    'label' => '\tab'
                ),
                array(
                    'value' => '[|]',
                    'label' => '[|]'
                ),
            )
        ));
        $fieldset->addField('feed_protector', 'select', array(
            'label' => Mage::helper('datafeed')->__('Fields enclosure'),
            'class' => 'txt-type refresh not-required',
            'maxlength' => 1,
            'name' => 'feed_protector',
            'values' => array(
                array(
                    'value' => '"',
                    'label' => '"'
                ),
                array(
                    'value' => "'",
                    'label' => "'"
                ),
                array(
                    'value' => "",
                    'label' => Mage::helper('datafeed')->__('none'),
                ),
            )
        ));



        $fieldset->addField('generate', 'hidden', array(
            'name' => 'generate',
            'value' => ''
        ));
        $fieldset->addField('continue', 'hidden', array(
            'name' => 'continue',
            'value' => ''
        ));
        $fieldset->addField('copy', 'hidden', array(
            'name' => 'copy',
            'value' => ''
        ));

        if (Mage::getSingleton('adminhtml/session')->getDatafeedData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getDatafeedData());
            Mage::getSingleton('adminhtml/session')->setDatafeedData(null);
        } elseif (Mage::registry('datafeed_data')) {
            $form->setValues(Mage::registry('datafeed_data')->getData());
        }

        return parent::_prepareForm();
    }

}
