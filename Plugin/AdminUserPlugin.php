<?php

namespace Unific\Extension\Plugin;

class AdminUserPlugin extends AbstractPlugin
{
    public function beforeSave($subject, $user)
    {
        $this->subject = 'admin/user/create';

        foreach ($this->getRequestCollection('Magento\User\Model\User::save', 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $user);
        }

        return [$user];
    }

    public function afterSave($subject, $user)
    {
        $this->subject = 'admin/user/create';

        foreach ($this->getRequestCollection('Magento\User\Model\User::save') as $request)
        {
            $this->handleCondition($request->getId(), $request, $user);
        }

        return $user;

    }
}
