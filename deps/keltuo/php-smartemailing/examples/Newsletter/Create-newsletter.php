<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\Api\Model\NewContactList;
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Newsletter-Create_newsletter
 */
// new Model Instance
$newModel = new \SmartemailingDeps\SmartEmailing\Api\Model\Newsletter(48, [22, 23]);
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->newsletter()->create($newModel);
// Get Response
\var_dump($list->getData());
// Response Object toString
echo (string) $list;
//
/*
{
 'statusCode': 201,
 'status': 'created',
 'data': {
   'id': 838
 },
 'message': ''
}
*/
