<?php

namespace Webkul\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Webkul\Affiliate\Contracts\AffiliatePayout as AffiliatePayoutContract;
use Webkul\User\Models\AdminProxy;

class AffiliatePayout extends Model implements AffiliatePayoutContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'affiliate_payouts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'payout_number',
        'amount',
        'payout_method',
        'payout_details',
        'status',
        'transaction_reference',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'payout_details' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the affiliate requesting the payout.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(AffiliateProxy::modelClass(), 'affiliate_id');
    }

    /**
     * Get the admin user who processed the payout.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(AdminProxy::modelClass(), 'processed_by');
    }

    /**
     * Get the transaction linked to this payout.
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(AffiliateWalletTransactionProxy::modelClass(), 'payout_id');
    }
}
