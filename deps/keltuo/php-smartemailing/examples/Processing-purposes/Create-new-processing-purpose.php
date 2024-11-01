<?php

namespace SmartemailingDeps;

require_once '../../vendor/autoload.php';
use SmartemailingDeps\SmartEmailing\SmartEmailing;
/**
 * https://app.smartemailing.cz/docs/api/v3/index.html#api-Processing_purposes-Create_new_Processing_purpose
 */
// new Model Instance
$newModel = new \SmartemailingDeps\SmartEmailing\Api\Model\Purpose(\SmartemailingDeps\SmartEmailing\Api\Model\Purpose::LAWFUL_CONTRACT, 'Pizza delivery', new \SmartemailingDeps\SmartEmailing\Api\Model\Period(\SmartemailingDeps\SmartEmailing\Api\Model\Period::DAYS, 30));
// Call Request
$list = (new SmartEmailing('usrname', 'apiKey'))->processingPurposes()->create($newModel);
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
   'id': 10,
   'created_at': '2021-11-12 16:24:11',
   'lawful_basis': 'contract',
   'name': 'Pizza delivery',
   'duration': {
     'value': 30,
     'unit': 'days'
   }
 },
 'message': ''
}
*/
