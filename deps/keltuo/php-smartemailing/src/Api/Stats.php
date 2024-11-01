<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api;

use SmartemailingDeps\SmartEmailing\Api\Model\Response\BaseResponse as Response;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\CampaignStats as SearchCampaignStats;
use SmartemailingDeps\SmartEmailing\Api\Model\Search\NewsletterStats as SearchNewsletterStats;
/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Stats
 * @package SmartEmailing\Api
 */
class Stats extends AbstractApi
{
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Stats-Get_campaign_sent_stats
     */
    public function getCampaignSent(?SearchCampaignStats $search = null) : Response
    {
        $search ??= new SearchCampaignStats();
        return new Response($this->get('campaign-stats-sent', $search->getAsQuery()));
    }
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Stats-Get_newsletter_stats_summaries
     */
    public function getNewsletterSummaries(?SearchNewsletterStats $search = null) : Response
    {
        $search ??= new SearchNewsletterStats();
        return new Response($this->get('newsletter-stats-summary', $search->getAsQuery()));
    }
}
