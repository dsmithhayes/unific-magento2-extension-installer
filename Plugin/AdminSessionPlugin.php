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

        foreach ($this->getRequestCollection('Magento\Backend\Model\Auth\Session::processLogin', 'before') as $request)
        {
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

        foreach ($this->getRequestCollection('Magento\Backend\Model\Auth\Session::processLogin') as $request)
        {
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

        foreach ($this->getRequestCollection('Magento\Backend\Model\Auth\Session::processLogout', 'before') as $request)
        {
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

        foreach ($this->getRequestCollection('Magento\Backend\Model\Auth\Session::processLogout') as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return $subject;
    }
}
