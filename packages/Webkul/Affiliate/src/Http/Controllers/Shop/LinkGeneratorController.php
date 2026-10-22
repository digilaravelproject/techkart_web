<?php

namespace Webkul\Affiliate\Http\Controllers\Shop;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Webkul\Affiliate\Repositories\AffiliateRepository;

class LinkGeneratorController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AffiliateRepository $affiliateRepository
    ) {}

    /**
     * Generate custom affiliate link for a given store URL.
     */
    public function generate(): JsonResponse
    {
        request()->validate([
            'url' => 'required|url',
        ]);

        $customer = Auth::guard('customer')->user();

        $affiliate = $this->affiliateRepository->getOrCreateForCustomer($customer->id);

        $url = request('url');

        $separator = parse_url($url, PHP_URL_QUERY) ? '&' : '?';

        $generatedUrl = $url.$separator.'ref='.$affiliate->referral_code;

        return new JsonResponse([
            'success' => true,
            'generated_url' => $generatedUrl,
            'referral_code' => $affiliate->referral_code,
        ]);
    }
}
