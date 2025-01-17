<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfields-Get_Customfield_values
 */
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->customFields()->getContactCustomFields();
// Call Request with Search filter
$search = (new \SmartemailingDeps\SmartEmailing\Api\Model\Search\ContactCustomFields())->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\ContactCustomFields::ID)->selectBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\ContactCustomFields::CONTACT_ID)->sortBy(\SmartemailingDeps\SmartEmailing\Api\Model\Search\ContactCustomFields::ID, \SmartemailingDeps\SmartEmailing\Api\Model\Search\ContactCustomFields::SORT_DESC)->setLimit(1);
$list = (new SmartEmailing('usrname', 'apiKey'))->customFields()->getContactCustomFields($search);
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
   'total_count': 1929695,
   'limit': 1,
   'offset': 0
 },
 'data': [
   {
     'id': 6765070,
     'contact_id': 3046485
   }
 ],
 'message': ''
}
*/
