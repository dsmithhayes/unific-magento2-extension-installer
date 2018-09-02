<?php

namespace Unific\Extension\Plugin;

class AdminUserPlugin extends AbstractPlugin
{
    public function beforeSave($subject, $user)
    {
        $this->subject = 'admin/user/create';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\User\Model\User::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'before'))
                 as $id => $request) {

            $this->handleCondition($request->getId(), $request, $user);
        }

        return [$user];
    }

    public function afterSave($subject, $user)
    {
        $this->subject = 'admin/user/create';

        foreach ($this->getRequestCollection()
                     ->addFieldToFilter('request_event', array('eq' => 'Magento\User\Model\User::save'))
                     ->addFieldToFilter('request_event_execution', array('eq' => 'after'))
                 as $id => $request) {

            $this->handleCondition($request->getId(), $request, $user);
        }

        return $user;

    }
}
