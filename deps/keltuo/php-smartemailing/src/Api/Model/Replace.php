<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model;

use SmartemailingDeps\JetBrains\PhpStorm\ArrayShape;
use SmartemailingDeps\JetBrains\PhpStorm\Pure;
class Replace extends AbstractModel implements ModelInterface
{
    /**
     * Dynamic tag name.
     * Eg: key = product_12 will cause all occurrences of {{replace_product_12}} string to be replaced.
     */
    protected string $key;
    /**
     * Dynamic tag content which will be used to replace dynamic tag.
     */
    protected string $content;
    public function __construct(string $key, string $content)
    {
        $this->setKey($key);
        $this->setContent($content);
    }
    #[Pure]
    public function getIdentifier() : string
    {
        return $this->getKey();
    }
    public function getKey() : string
    {
        return $this->key;
    }
    public function setKey(string $key) : Replace
    {
        $this->key = $key;
        return $this;
    }
    public function getContent() : string
    {
        return $this->content;
    }
    public function setContent(string $content) : Replace
    {
        $this->content = $content;
        return $this;
    }
    #[ArrayShape(['key' => "string", 'content' => "string"])]
    public function toArray() : array
    {
        return ['key' => $this->getKey(), 'content' => $this->getContent()];
    }
}
