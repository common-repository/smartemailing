<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api;

use SmartemailingDeps\SmartEmailing\Api\Model\Response\BaseResponse as Response;
/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Transactional_emails
 * @package SmartEmailing\Api
 */
class TransactionalEmails extends AbstractApi
{
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Transactional_emails-Get_transactional_email_ids
     */
    public function getListCreated() : Response
    {
        return new Response($this->get('transactional-emails-ids'));
    }
}
