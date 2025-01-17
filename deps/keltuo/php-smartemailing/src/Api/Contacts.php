<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api;

use SmartemailingDeps\SmartEmailing\Api\Model\ChangeEmailAddress;
use SmartemailingDeps\SmartEmailing\Api\Model\Response\BaseResponse as Response;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\Contacts as SearchContact;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\SingleContact as SearchSingleContact;
/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts
 * @package SmartEmailing\Api
 */
class Contacts extends AbstractApi
{
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Change_Contacts_e_mail_address
     */
    public function changeEmailAddress(ChangeEmailAddress $changeEmailAddress) : Response
    {
        return new Response($this->post('change-emailaddress', $changeEmailAddress->toArray()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Forget_contact
     */
    public function forgetContact(int $idContact) : Response
    {
        return new Response($this->delete($this->replaceUrlParameters('contacts/forget/:id', ['id' => $idContact])));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Get_Contacts_with_lists_and_customfield_values
     */
    public function getList(?SearchContact $search = null) : Response
    {
        $search ??= new SearchContact();
        return new Response($this->get('contacts', $search->getAsQuery()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Get_Single_contact_with_lists_and_customfield_values
     */
    public function getSingle(int $idContact, ?SearchSingleContact $search = null) : Response
    {
        $search ??= new SearchSingleContact();
        return new Response($this->get($this->replaceUrlParameters('contacts/:id', ['id' => $idContact]), $search->getAsQuery()));
    }
}
