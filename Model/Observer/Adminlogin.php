<?php

/**
 * MagePrince
 * Copyright (C) 2018 Mageprince
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category MagePrince
 * @package Prince_Adminlogs
 * @copyright Copyright (c) 2018 MagePrince
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince
 */

namespace Prince\Adminlogs\Model\Observer;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class Adminlogin implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Prince\Adminlogs\Model\AdminlogsFactory
     */
    protected $_adminLogsModel;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_ipAddress;

    /**
     * Adminlogin constructor.
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Prince\Adminlogs\Model\AdminlogsFactory $adminLogsModel
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $ipAddress
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Prince\Adminlogs\Model\AdminlogsFactory $adminLogsModel,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $ipAddress
    ) {
        $this->_authSession = $authSession;
        $this->_adminLogsModel = $adminLogsModel;
        $this->_ipAddress = $ipAddress;
    }

    public function execute(Observer $observer)
    {
        $user = $this->_authSession->getUser();
        $ipAddress = $this->_ipAddress->getRemoteAddress();
        if ($ipAddress=="127.0.0.1") { $ipAddress=$this->getUserIpAddr(); }
		$model = $this->_adminLogsModel->create();
        $model->setUsername($user->getUsername());
        $model->setIpaddress($ipAddress);
        $model->setStatus(1);
        $model->setDate(date('Y-m-d H:i:s'));
        $model->save();
    }
	
	
	public function getUserIpAddr(){ 
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){ $ip = $_SERVER['HTTP_CLIENT_IP']; }
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
		else{ $ip = $_SERVER['REMOTE_ADDR']; }
		return $ip; 
	}
	
}

