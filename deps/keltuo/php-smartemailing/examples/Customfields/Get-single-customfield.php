<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfields-Get_single_Customfield
 */
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->customFields()->getSingle(119);
// Returns single without filter
// Call Request with Search filter
$search = (new \SmartemailingDeps\SmartEmailing\Api\Model\Search\CustomFields())->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\CustomFields::ID)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\CustomFields::NAME);
$list = (new SmartEmailing('usrname', 'apiKey'))->customFields()->getSingle(119, $search);
// Get single with filtered fields
// Get Response
\var_dump($list->getData());
// Response Object toString
echo (string) $list;
//
/*
{
 'statusCode': 200,
 'status': 'ok',
 'data': {
   'id': 119,
   'name': 'Test'
 },
 'message': ''
}
*/
