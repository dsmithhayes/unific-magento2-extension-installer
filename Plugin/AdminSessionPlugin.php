<?php

namespace Unific\Extension\Plugin;

class AdminSessionPlugin extends AbstractPlugin
{
    protected $subject = 'admin/login';

    /**
     * @param $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundProcessLogin($subject, callable $proceed)
    {
        $this->adminUser = $subject;

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        $result = $proceed();

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundProcessLogout($subject, callable $proceed)
    {
        $this->subject = 'admin/logout';
        $this->adminUser = $subject;

        foreach ($this->getRequestCollection('before') as $request)
        {
            $this->handleConditions($request->getId(), $request, $subject);
        }

        $result = $proceed();

        foreach ($this->getRequestCollection() as $request)
        {
            $this->handleConditions($request->getId(), $request);
        }

        return $result;
    }
}