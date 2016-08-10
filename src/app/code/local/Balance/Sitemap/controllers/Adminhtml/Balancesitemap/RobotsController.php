<?php

class Balance_Sitemap_Adminhtml_Balancesitemap_RobotsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_BlockController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('balance_sitemap/robots')
            ->_addBreadcrumb(Mage::helper('balance_sitemap')->__('Sitemap'), Mage::helper('balance_sitemap')->__('Sitemap'))
            ->_addBreadcrumb(Mage::helper('balance_sitemap')->__('Robots'), Mage::helper('balance_sitemap')->__('Robots'))
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {        
        $this->_title($this->__('Sitemap'))->_title($this->__('Robots'));        
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('balance_sitemap/adminhtml_robots'))
            ->renderLayout();        
    }

    /**
     * Create new CMS block
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS block
     */
    public function editAction()
    {
        $this->_title($this->__('Sitemap'))->_title($this->__('Robots'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('robots_id');
        $model = Mage::getModel('balance_sitemap/robots');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('balance_sitemap')->__('Robots.txt file no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Robots.txt'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('balance_sitemap_robots', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                    $id ? Mage::helper('balance_sitemap')->__('Edit Block') : Mage::helper('balance_sitemap')->__('New Block'), 
                    $id ? Mage::helper('balance_sitemap')->__('Edit Robots.txt') : Mage::helper('balance_sitemap')->__('New Robots.txt'))
            ->_addContent($this->getLayout()->createBlock('balance_sitemap/adminhtml_robots_edit'))
            ->renderLayout();
     
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('robots_id');
            $model = Mage::getModel('balance_sitemap/robots')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('balance_sitemap')->__('This Robots.txt no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('balance_sitemap')->__('The Robots.txt has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('robots_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('robots_id' => $this->getRequest()->getParam('robots_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('robots_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('balance_sitemap/robots');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('balance_sitemap')->__('The Robots.txt has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('robots_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('balance_sitemap')->__('Unable to find a Robots.txt to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('balance_sitemap/robots');
    }
}
