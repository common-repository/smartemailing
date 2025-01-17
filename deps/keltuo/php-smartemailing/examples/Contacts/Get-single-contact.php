<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfields-Get_single_Customfield
 */
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->contacts()->getSingle(6715186);
// Returns single without filter
// Call Request with Search filter
$search = (new \SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts())->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts::ID)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts::NAME)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts::EMAIL_ADDRESS);
$list = (new SmartEmailing('usrname', 'apiKey'))->contacts()->getSingle(6715186, $search);
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
   'id': 6715186,
   'name': 'Martin',
   'emailaddress': 'test@emailing.cz'
 },
 'message': ''
}
*/
