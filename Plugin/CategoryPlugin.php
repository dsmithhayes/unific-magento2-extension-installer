<?php

namespace Unific\Extension\Plugin;

class CategoryPlugin extends AbstractPlugin
{
    protected $entity = 'category';
    protected $subject = 'category/create';

    /**
     * @param $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundSave($subject, callable $proceed)
    {
        $this->setSubject($subject);
        $this->category = $subject;

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

    public function setSubject($category)
    {
        if($category->getCreatedAt() != $category->getUpdatedAt())
        {
            $this->subject = 'category/update';
        }
    }
}
