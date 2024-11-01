<?php

namespace SmartemailingDeps\GuzzleHttp;

use SmartemailingDeps\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string;
}
