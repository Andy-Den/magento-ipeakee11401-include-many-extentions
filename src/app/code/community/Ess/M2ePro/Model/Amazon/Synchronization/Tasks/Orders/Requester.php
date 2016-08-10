<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders_Requester extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Orders_Get_Items
{
    // ########################################

    protected function makeResponserModel()
    {
        return 'M2ePro/Amazon_Synchronization_Tasks_Orders_Responser';
    }

    protected function setLocks($messageHash)
    {
        /** @var $lockItem Ess_M2ePro_Model_LockItem */
        $lockItem = Mage::getModel('M2ePro/LockItem');

        $tempNick = Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX;
        $tempNick .= '_'.$this->account->getId().'_'.$this->marketplace->getId();

        $lockItem->setNick($tempNick);
        $lockItem->create();

        $this->account->addObjectLock(NULL,$messageHash);
        $this->account->addObjectLock('synchronization',$messageHash);
        $this->account->addObjectLock('synchronization_amazon',$messageHash);
        $this->account->addObjectLock(Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX,$messageHash);

        $this->marketplace->addObjectLock(NULL,$messageHash);
        $this->marketplace->addObjectLock('synchronization',$messageHash);
        $this->marketplace->addObjectLock('synchronization_amazon',$messageHash);
        $this->marketplace->addObjectLock(Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX,$messageHash);
    }

    // ########################################
}