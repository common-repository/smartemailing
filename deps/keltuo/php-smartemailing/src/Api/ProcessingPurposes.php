<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api;

use SmartemailingDeps\SmartEmailing\Api\Model\Purpose;
use SmartemailingDeps\SmartEmailing\Api\Model\Response\BaseResponse as Response;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\PurposeConnections as SearchPurposeConnections;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\Purposes as SearchPurposes;
/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Processing_purposes
 * @package SmartEmailing\Api
 */
class ProcessingPurposes extends AbstractApi
{
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Processing_purposes-Create_new_Processing_purpose
     */
    public function create(Purpose $purpose) : Response
    {
        return new Response($this->post('purposes', $purpose->toArray()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Processing_purposes-Get_Processing_purpose_connections
     */
    public function getListConnections(?SearchPurposeConnections $search = null) : Response
    {
        $search ??= new SearchPurposeConnections();
        return new Response($this->get('purpose-connections', $search->getAsQuery()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Processing_purposes-Get_Processing_purposes
     */
    public function getList(?SearchPurposes $search = null) : Response
    {
        $search ??= new SearchPurposes();
        return new Response($this->get('purposes', $search->getAsQuery()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Processing_purposes-Revoke_Processing_purpose_connection
     */
    public function revoke(int $idPurposeConnection) : Response
    {
        return new Response($this->delete($this->replaceUrlParameters('purpose-connections/:id', ['id' => $idPurposeConnection])));
    }
}
