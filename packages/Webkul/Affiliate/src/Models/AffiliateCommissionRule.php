<?php

namespace Webkul\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Affiliate\Contracts\AffiliateCommissionRule as AffiliateCommissionRuleContract;
use Webkul\Category\Models\CategoryProxy;
use Webkul\Product\Models\ProductProxy;

class AffiliateCommissionRule extends Model implements AffiliateCommissionRuleContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'affiliate_commission_rules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'commission_type',
        'commission_value',
        'is_excluded',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'commission_value' => 'float',
        'is_excluded' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Get the associated product if entity_type is product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass(), 'entity_id');
    }

    /**
     * Get the associated category if entity_type is category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryProxy::modelClass(), 'entity_id');
    }
}
