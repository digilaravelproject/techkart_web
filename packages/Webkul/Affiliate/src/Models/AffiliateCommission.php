<?php

namespace Webkul\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Affiliate\Contracts\AffiliateCommission as AffiliateCommissionContract;
use Webkul\Product\Models\ProductProxy;
use Webkul\Sales\Models\OrderItemProxy;
use Webkul\Sales\Models\OrderProxy;

class AffiliateCommission extends Model implements AffiliateCommissionContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'affiliate_commissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'order_id',
        'order_item_id',
        'product_id',
        'item_price',
        'item_qty',
        'applied_tier',
        'commission_type',
        'commission_rate',
        'commission_amount',
        'status',
        'clears_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'item_price' => 'float',
        'item_qty' => 'integer',
        'commission_rate' => 'float',
        'commission_amount' => 'float',
        'clears_at' => 'datetime',
    ];

    /**
     * Get the affiliate that earned the commission.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(AffiliateProxy::modelClass(), 'affiliate_id');
    }

    /**
     * Get the order associated with the commission.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(OrderProxy::modelClass(), 'order_id');
    }

    /**
     * Get the order item associated with the commission.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItemProxy::modelClass(), 'order_item_id');
    }

    /**
     * Get the product associated with the commission.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass(), 'product_id');
    }
}
