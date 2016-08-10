<?php

class Balance_Sitemap_Block_Adminhtml_Robots_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('balance_sitemap_robots_form');
        $this->setTitle(Mage::helper('balance_sitemap')->__('Robots.txt Information'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
       
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('balance_sitemap_robots');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('balance_sitemap_robots_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('balance_sitemap')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getRobotsId()) {
            $fieldset->addField('robots_id', 'hidden', array(
                'name' => 'robots_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('balance_sitemap')->__('Robots.txt Title'),
            'title'     => Mage::helper('balance_sitemap')->__('Robots.txt Title'),
            'required'  => true,
        ));

        

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field =$fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('balance_sitemap')->__('Store View'),
                'title'     => Mage::helper('balance_sitemap')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('balance_sitemap')->__('Status'),
            'title'     => Mage::helper('balance_sitemap')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('balance_sitemap')->__('Enabled'),
                '0' => Mage::helper('balance_sitemap')->__('Disabled'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('balance_sitemap')->__('Content'),
            'title'     => Mage::helper('balance_sitemap')->__('Content'),
            'style'     => 'height:36em',
            'required'  => true,            
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
