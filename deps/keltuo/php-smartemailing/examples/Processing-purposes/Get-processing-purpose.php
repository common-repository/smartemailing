<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Processing_purposes-Get_Processing_purposes
 */
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->processingPurposes()->getList();
// Call Request with Search filter
$search = (new \SmartemailingDeps\SmartEmailing\Api\Model\Search\Purposes())->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Purposes::ID)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Purposes::NAME)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Purposes::DURATION)->sortBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Purposes::ID, \SmartemailingDeps\SmartEmailing\Api\Model\Search\Purposes::SORT_DESC)->setLimit(1);
$list = (new SmartEmailing('usrname', 'apiKey'))->processingPurposes()->getList($search);
// Get all with search filter
// Get Response
\var_dump($list->getData());
// Response Object toString
echo (string) $list;
//
/*
{
 'statusCode': 200,
 'status': 'ok',
 'meta': {
   'displayed_count': 1,
   'total_count': 4,
   'limit': 1,
   'offset': 0
 },
 'data': [
   {
     'id': 10,
     'name': 'Pizza delivery',
     'duration': {
       'value': 30,
       'unit': 'days'
     }
   }
 ],
 'message': ''
}
*/
