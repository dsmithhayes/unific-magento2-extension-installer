<?php

namespace Unific\Extension\Plugin;

class AdminSessionPlugin extends AbstractPlugin
{
    public function beforeProcessLogin($subject, $user)
    {
        $this->subject = 'admin/login';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Backend\Model\Auth\Session::processLogin'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $user);
        }

        return [$user];
    }

    public function afterProcessLogin($subject, $user)
    {
        $this->subject = 'admin/login';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Backend\Model\Auth\Session::processLogin'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $user);
        }

        return $user;

    }

    public function beforeProcessLogout($subject, $user)
    {
        $this->subject = 'admin/logout';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Backend\Model\Auth\Session::processLogout'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $user);
        }

        return [$user];
    }

    public function afterProcessLogout($subject, $user)
    {
        $this->subject = 'admin/logout';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Backend\Model\Auth\Session::processLogout'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($id, $request, $user);
        }

        return $user;
    }
}
