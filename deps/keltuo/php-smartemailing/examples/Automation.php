<?php

namespace SmartemailingDeps;

require_once '../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Automation-Trigger_event
 */
// new Model Instance
$triggerEventBag = new \SmartemailingDeps\SmartEmailing\Api\Model\Bag\TriggerEventBag();
$triggerEventBag->add(new \SmartemailingDeps\SmartEmailing\Api\Model\Event('info@youremail.cz', 'other-event', ['something' => 'test']));
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->automation()->triggerEvent($triggerEventBag);
// Get Response
\var_dump($list->getData());
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
