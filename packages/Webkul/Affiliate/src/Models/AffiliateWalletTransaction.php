<?php

namespace Webkul\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Affiliate\Contracts\AffiliateWalletTransaction as AffiliateWalletTransactionContract;
use Webkul\Sales\Models\OrderProxy;

class AffiliateWalletTransaction extends Model implements AffiliateWalletTransactionContract
{
    /**
     * Disable updated_at since transactions are immutable ledger entries.
     */
    const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'affiliate_wallet_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'transaction_number',
        'order_id',
        'payout_id',
        'type',
        'amount',
        'balance_after',
        'status',
        'title',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'balance_after' => 'float',
    ];

    /**
     * Get the affiliate associated with the transaction.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(AffiliateProxy::modelClass(), 'affiliate_id');
    }

    /**
     * Get the order associated with the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(OrderProxy::modelClass(), 'order_id');
    }

    /**
     * Get the payout associated with the transaction.
     */
    public function payout(): BelongsTo
    {
        return $this->belongsTo(AffiliatePayoutProxy::modelClass(), 'payout_id');
    }
}
