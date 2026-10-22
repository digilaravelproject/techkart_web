<?php

namespace Webkul\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Affiliate\Contracts\AffiliateWallet as AffiliateWalletContract;

class AffiliateWallet extends Model implements AffiliateWalletContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'affiliate_wallets';

    /**
     * Disable updated_at or use timestamps.
     * Note: Schema has updated_at, let's enable standard timestamps or handle them.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'available_balance',
        'pending_balance',
        'locked_balance',
        'total_earned',
        'total_withdrawn',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'available_balance' => 'float',
        'pending_balance' => 'float',
        'locked_balance' => 'float',
        'total_earned' => 'float',
        'total_withdrawn' => 'float',
    ];

    /**
     * Get the affiliate that owns the wallet.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(AffiliateProxy::modelClass(), 'affiliate_id');
    }

    /**
     * Get all transactions for this wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(AffiliateWalletTransactionProxy::modelClass(), 'affiliate_id', 'affiliate_id');
    }
}
