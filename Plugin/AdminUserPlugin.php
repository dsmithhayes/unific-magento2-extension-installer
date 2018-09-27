<?php

namespace Unific\Extension\Plugin;

class AdminUserPlugin extends AbstractPlugin
{
    /**
     * @param $subject
     * @param callable $proceed
     * @param $user
     * @return mixed
     */
    public function aroundSave($subject, callable $proceed, $user)
    {
        $this->subject = 'admin/user/create';
        $this->adminUser = $user;

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed($user);

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }
}
