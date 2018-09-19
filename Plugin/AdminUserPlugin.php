<?php

namespace Unific\Extension\Plugin;

class AdminUserPlugin extends AbstractPlugin
{
    public function beforeSave($subject, $user)
    {
        $this->subject = 'admin/user/create';

        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $user);
        }

        return [$user];
    }

    public function afterSave($subject, $user)
    {
        $this->subject = 'admin/user/create';

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request, $user);
        }

        return $user;

    }
}
