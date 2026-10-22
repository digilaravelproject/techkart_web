<?php

namespace Webkul\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Webkul\Affiliate\Contracts\Affiliate as AffiliateContract;
use Webkul\Customer\Models\CustomerProxy;

class Affiliate extends Model implements AffiliateContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'affiliates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'referral_code',
        'status',
        'payout_method',
        'payout_details',
        'total_clicks',
        'total_orders',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payout_details' => 'array',
        'total_clicks' => 'integer',
        'total_orders' => 'integer',
    ];

    /**
     * Get the customer that owns the affiliate profile.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerProxy::modelClass(), 'customer_id');
    }

    /**
     * Get the wallet associated with the affiliate.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(AffiliateWalletProxy::modelClass(), 'affiliate_id');
    }

    /**
     * Get the commissions for the affiliate.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommissionProxy::modelClass(), 'affiliate_id');
    }

    /**
     * Get the payout requests for the affiliate.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayoutProxy::modelClass(), 'affiliate_id');
    }

    /**
     * Get the transactions for the affiliate wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(AffiliateWalletTransactionProxy::modelClass(), 'affiliate_id');
    }
}
