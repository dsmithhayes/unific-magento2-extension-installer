<?php

namespace Unific\Extension\Plugin;

class AdminSessionPlugin extends AbstractPlugin
{
    protected $subject = 'admin/login';

    /**
     * @param $subject
     * @return array
     */
    public function beforeProcessLogin($subject)
    {
        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
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
        foreach ($this->getRequestCollection($this->subject) as $request)
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

        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
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

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return $subject;
    }
}
