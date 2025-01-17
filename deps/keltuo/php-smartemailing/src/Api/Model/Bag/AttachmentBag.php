<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Bag;

use SmartemailingDeps\SmartEmailing\Api\Model\Attachment;
class AttachmentBag extends AbstractBag
{
    public function add(Attachment $model) : AttachmentBag
    {
        $this->insertEntry($model);
        return $this;
    }
    public function create(string $fileName, string $contentType, string $data) : Attachment
    {
        $model = new Attachment($fileName, $contentType, $data);
        $this->add($model);
        return $model;
    }
}
