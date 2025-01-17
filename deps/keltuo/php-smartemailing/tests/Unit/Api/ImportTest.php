<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Test\Unit\Api;

use SmartemailingDeps\GuzzleHttp\Psr7\Response;
use SmartemailingDeps\SmartEmailing\Api\Model\Bag\ContactBag;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\ContactDetail;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\ContactList;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\CustomField;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\Purpose;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\Settings;
use SmartemailingDeps\SmartEmailing\Api\Model\Import;
use SmartemailingDeps\SmartEmailing\Test\TestCase;
class ImportTest extends TestCase
{
    public function testShouldImportContacts() : void
    {
        $expectedArray = '{
            "status": "created",
            "meta": [],
            "contacts_map": [
                {
                    "emailaddress": "martin@smartemailing.cz",
                    "contact_id": 123
                },
                {
                    "emailaddress": "martin1@smartemailing.cz",
                    "contact_id": 124
                }
            ],
            "double_opt_in_map": []
        }';
        $contact = (new ContactDetail('martin@smartemailing.cz'))->setName('Martin');
        $contact->getContactListBag()->add(new ContactList(1, ContactList::CONFIRMED))->add(new ContactList(2, ContactList::CONFIRMED));
        $contact->getCustomFieldBag()->add(new CustomField(1, [1, 3]))->add(new CustomField(8, [], '2016-01-10 13:53:03'));
        $contact->getPurposeBag()->add(new Purpose(2))->add(new Purpose(3, '2018-01-11 10:30:00', '2023-01-11 10:30:00'));
        $contact1 = (new ContactDetail('martin1@smartemailing.cz'))->setName('Martin');
        $contact1->getContactListBag()->add(new ContactList(1, ContactList::CONFIRMED))->add(new ContactList(2, ContactList::UNSUBSCRIBED));
        $contact1->getPurposeBag()->add(new Purpose(1, '2018-01-10 12:00:01'));
        $contactBag = (new ContactBag())->add($contact)->add($contact1);
        $importBag = new Import($contactBag, new Settings());
        $this->assertEquals(\json_decode($this->getExpectedRequest(), \true), \json_decode(\json_encode($importBag), \true));
        $api = $this->getApiMock();
        $api->expects($this->once())->method('post')->with('import', $importBag->toArray())->will($this->returnValue(new Response(201, [], $expectedArray)));
        /** @var \SmartEmailing\Api\Import $api */
        $response = $api->contacts($importBag);
        $expectedObject = \json_decode($expectedArray);
        $this->assertEquals($expectedObject->contacts_map, $response->getData());
        $this->assertEquals(empty($expectedObject->meta) ? null : $expectedObject->meta, $response->getMeta());
        $this->assertEquals($expectedObject->status, $response->getStatus());
        $this->assertTrue($response->isSuccess());
        $this->assertNull($api->contacts(new Import(new ContactBag())));
    }
    protected function getExpectedRequest() : string
    {
        return '
            {
                "settings": {
                    "update": true,
                    "add_namedays": true,
                    "add_genders": true,
                    "add_salutions": true,
                    "preserve_unsubscribed": true,
                    "skip_invalid_emails": false
                },
                "data": [
                    {
                        "emailaddress": "martin@smartemailing.cz",
                        "name": "Martin",
                        "contactlists": [
                            {
                                "id": 1,
                                "status": "confirmed"
                            },
                            {
                                "id": 2,
                                "status": "confirmed"
                            }
                        ],
                        "customfields": [
                            {
                                "id": 1,
                                "options": [
                                    1,
                                    3
                                ]
                            },
                            {
                                "id": 8,
                                "value": "2016-01-10 13:53:03"
                            }
                        ],
                        "purposes": [
                            {
                                "id": 2
                            },
                            {
                                "id": 3,
                                "valid_from": "2018-01-11 10:30:00",
                                "valid_to": "2023-01-11 10:30:00"
                            }
                        ]
                    },
                    {
                        "emailaddress": "martin1@smartemailing.cz",
                        "name": "Martin",
                        "contactlists": [
                            {
                                "id": 1,
                                "status": "confirmed"
                            },
                            {
                                "id": 2,
                                "status": "unsubscribed"
                            }
                        ],
                        "purposes": [
                            {
                                "id": 1,
                                "valid_from": "2018-01-10 12:00:01"
                            }
                        ]
                    }
                ]
            }
        ';
    }
    protected function getApiClass() : string
    {
        return \SmartemailingDeps\SmartEmailing\Api\Import::class;
    }
}
