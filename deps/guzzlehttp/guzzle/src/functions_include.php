<?php

namespace SmartemailingDeps;

// Don't redefine the functions if included multiple times.
if (!\function_exists('SmartemailingDeps\\GuzzleHttp\\describe_type')) {
    require __DIR__ . '/functions.php';
}