<?php

namespace Smartemailing\Integrations;

use SmartemailingDeps\SmartEmailing\Api\AbstractApi;
use SmartemailingDeps\SmartEmailing\Api\Model\Response\BaseResponse as Response;
use SmartemailingDeps\SmartEmailing\SmartEmailing;


class AccountInfo extends AbstractApi {
	public function __construct( SmartEmailing $smartEmailing ) {
		parent::__construct( $smartEmailing );
	}

	public function getAccountInfo(): Response {
		return new Response( $this->get( 'account-info' ) );
	}
}
