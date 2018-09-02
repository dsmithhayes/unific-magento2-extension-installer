<?php

namespace Unific\Extension\Plugin;

class AdminSessionPlugin extends AbstractPlugin
{

    /**
     * @param $subject
     * @return array
     */
    public function beforeProcessLogin($subject)
    {
        $this->subject = 'admin/login';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Backend\Model\Auth\Session::processLogin'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($request->getId(), $request, $subject);
        }

        return [$subject];
    }

    /**
     * @param $subject
     * @return array
     */
    public function afterProcessLogin($subject)
    {
        $this->subject = 'admin/login';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Backend\Model\Auth\Session::processLogin'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($request->getId(), $request, $subject);
        }

        return $subject;

    }

    /**
     * @param $subject
     * @return array
     */
    public function beforeProcessLogout($subject)
    {
        $this->subject = 'admin/logout';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Backend\Model\Auth\Session::processLogout'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($request->getId(), $request, $subject);
        }

        return [$subject];
    }

    /**
     * @param $subject
     * @return array
     */
    public function afterProcessLogout($subject)
    {
        $this->subject = 'admin/logout';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\Backend\Model\Auth\Session::processLogout'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($request->getId(), $request, $subject);
        }

        return $subject;
    }
}
