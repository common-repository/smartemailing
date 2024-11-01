<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfield_Options-Get_Customfield_options
 */
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->customFieldOptions()->getList();
// Call Request with Search filter
$search = (new \SmartemailingDeps\SmartEmailing\Api\Model\Search\CustomFieldOptions())->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\CustomFieldOptions::ID)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\CustomFieldOptions::NAME)->sortBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\CustomFieldOptions::ID, \SmartemailingDeps\SmartEmailing\Api\Model\Search\CustomFieldOptions::SORT_DESC)->setLimit(1);
$list = (new SmartEmailing('usrname', 'apiKey'))->customFieldOptions()->getList($search);
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
   'total_count': 10,
   'limit': 1,
   'offset': 0
 },
 'data': [
   {
     'id': 20,
     'name': 'Test hodnota 4'
   }
 ],
 'message': ''
}
*/
