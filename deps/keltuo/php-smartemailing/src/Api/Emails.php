<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api;

use SmartemailingDeps\SmartEmailing\Api\Model\Email;
use SmartemailingDeps\SmartEmailing\Api\Model\EmailTemplate;
use SmartemailingDeps\SmartEmailing\Api\Model\Response\BaseResponse as Response;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\Emails as SearchEmails;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\SingleEmail as SearchSingleEmail;
/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Emails
 * @package SmartEmailing\Api
 */
class Emails extends AbstractApi
{
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Emails-Create_e_mail_from_template
     */
    public function createFromTemplate(EmailTemplate $emailTemplate) : Response
    {
        return new Response($this->post('emails/create-from-template', $emailTemplate->toArray()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Emails-Create_new_E_mail
     */
    public function create(Email $email) : Response
    {
        return new Response($this->post('emails', $email->toArray()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Emails-Get_E_mails
     */
    public function getList(?SearchEmails $search = null) : Response
    {
        $search ??= new SearchEmails();
        return new Response($this->get('emails', $search->getAsQuery()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Emails-Get_confirmation_emails
     */
    public function getConfirmationList(?SearchEmails $search = null) : Response
    {
        $search ??= new SearchEmails();
        return new Response($this->get('confirmation-emails', $search->getAsQuery()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Emails-Get_single_E_mail
     */
    public function getSingle(int $idEmail, ?SearchSingleEmail $search = null) : Response
    {
        $search ??= new SearchSingleEmail();
        return new Response($this->get($this->replaceUrlParameters('emails/:id', ['id' => $idEmail]), $search->getAsQuery()));
    }
}
