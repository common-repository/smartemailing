<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Webhooks-Creates_new_webhook
 */
// new Model Instance
$newModel = new \SmartemailingDeps\SmartEmailing\Api\Model\Webhook('http://example.com/notices/new_contact', \SmartemailingDeps\SmartEmailing\Api\Model\Webhook::EVENT_NEW_CONTACT);
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->webhooks()->create($newModel);
// Get Response
\var_dump($list->getStatus());
// Response Object toString
echo (string) $list;
//
/*
{
 'statusCode': 201,
 'status': 'created',
 'data': [],
 'message': ''
}
*/
