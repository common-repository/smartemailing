<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Get_Contacts_with_lists_and_customfield_values
 */
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->contacts()->getList();
// Call Request with Search filter
$search = (new \SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts())->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts::ID)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts::NAME)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts::EMAIL_ADDRESS)->sortBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts::ID, \SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts::SORT_DESC)->setLimit(1);
$list = (new SmartEmailing('usrname', 'apiKey'))->contacts()->getList($search);
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
   'total_count': 465538,
   'limit': 1,
   'offset': 0
 },
 'data': [
   {
     'id': 6715186,
     'name': 'Martin',
     'emailaddress': 'test@emailing.cz'
   }
 ],
 'message': ''
}
*/
