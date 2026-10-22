<?php

namespace Webkul\Affiliate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Affiliate\Repositories\AffiliateRepository;

class TrackAffiliateReferral
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(
        protected AffiliateRepository $affiliateRepository
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $refCode = $request->query('ref');

        if (! empty($refCode)) {
            $affiliate = $this->affiliateRepository->findOneWhere([
                'referral_code' => $refCode,
                'status' => 'active',
            ]);

            if ($affiliate) {
                $days = (int) (core()->getConfigData('affiliate.general.cookie_duration') ?? 30);
                $minutes = $days * 24 * 60;

                Cookie::queue('beenzo_affiliate_ref', $refCode, $minutes);
                session(['beenzo_affiliate_ref' => $refCode]);

                $cacheKey = 'affiliate_click_'.$affiliate->id.'_'.md5($request->ip() ?? 'unknown');

                if (! Cache::has($cacheKey)) {
                    Cache::put($cacheKey, true, now()->addMinutes(10));
                    $this->affiliateRepository->incrementClicks($affiliate->id);
                }
            }
        }

        return $next($request);
    }
}
