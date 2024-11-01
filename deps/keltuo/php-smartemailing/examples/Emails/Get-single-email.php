<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Emails-Get_single_E_mail
 */
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->emails()->getSingle(48);
// Returns single without filter
// Call Request with Search filter
$search = (new \SmartemailingDeps\SmartEmailing\Api\Model\Search\SingleEmail())->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\SingleEmail::ID)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\SingleEmail::NAME)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\SingleEmail::TEXT_BODY);
$list = (new SmartEmailing('usrname', 'apiKey'))->emails()->getSingle(48, $search);
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
   'id': 48,
   'name': 'Template 1',
   'textbody': "Your custom email: adsf"
 },
 'message': ''
}
*/
