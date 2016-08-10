<?php
/**
 *  extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Balance Deployment module to newer versions in the future.
 * If you wish to customize the Balance Deployment module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Balance
 * @package    Balance_Deployment
 * @copyright  Copyright (C) 2013 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Balance
 * @package    Balance_Deployment
 * @subpackage Helper
 * @author     Richard <richard@balanceinternet.com.au>
 */
class Balance_Deployment_Helper_Server extends Mage_Core_Helper_Data
{
	public function getAWSInternalIp()
	{
		$cmd = "GET http://169.254.169.254/latest/meta-data/local-ipv4";
		$ip = shell_exec($cmd);
		return $ip;
	}
	
	public function getAWSExternalIp()
	{
		$cmd = "GET http://169.254.169.254/latest/meta-data/public-ipv4";
		$ip = shell_exec($cmd);
		return $ip;
	}
	
	public function getInternalIp()
	{
		$ip = gethostbyname(trim(`hostname`));
		return $ip;
	}
}
