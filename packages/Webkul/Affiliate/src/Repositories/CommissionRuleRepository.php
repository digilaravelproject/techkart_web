<?php

namespace Webkul\Affiliate\Repositories;

use Webkul\Affiliate\Contracts\AffiliateCommissionRule;
use Webkul\Core\Eloquent\Repository;

class CommissionRuleRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return AffiliateCommissionRule::class;
    }

    /**
     * Get rule by entity type and ID.
     */
    public function getRule(string $entityType, int $entityId): ?AffiliateCommissionRule
    {
        return $this->model
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('status', 1)
            ->first();
    }
}
