<?php

namespace Unific\Extension\Plugin;

class CategoryPlugin extends AbstractPlugin
{
    protected $entity = 'category';
    protected $subject = 'category/create';

    /**
     * @param $subject
     * @return array
     */
    public function beforeSave($subject)
    {
        $this->setSubject($subject);

        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return [$subject];
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function afterSave($subject)
    {
        $this->setSubject($subject);

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $this->handleCondition($request->getId(), $request, $subject);
        }

        return $subject;
    }

    public function setSubject($category)
    {
        if($category->getCreatedAt() != $category->getUpdatedAt())
        {
            $this->subject = 'category/update';
        }
    }
}
