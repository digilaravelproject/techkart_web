<?php

namespace Webkul\Affiliate\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Affiliate\Services\AffiliateGraphQLService;

class HandleAffiliateGraphQL
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(
        protected AffiliateGraphQLService $graphQLService
    ) {}

    /**
     * Handle an incoming GraphQL request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        if (! str_starts_with($path, '/api/graphql') && ! str_starts_with($path, '/graphql')) {
            return $next($request);
        }

        if ($request->method() !== 'POST') {
            return $next($request);
        }

        $rawBody = $request->getContent();
        $payload = json_decode($rawBody, true);

        if (! is_array($payload) || empty($payload['query'])) {
            return $next($request);
        }

        $query = (string) $payload['query'];
        $variables = is_array($payload['variables'] ?? null) ? $payload['variables'] : [];

        $affiliateOperations = [
            'affiliateDashboard',
            'affiliateAccount',
            'productShareInfo',
            'affiliateTransactions',
            'affiliateCommissions',
            'affiliatePayouts',
            'registerAffiliate',
            'generateAffiliateLink',
            'updateAffiliatePayoutSettings',
            'requestAffiliatePayout',
            'trackAffiliateReferral',
        ];

        $matchedOperation = null;
        foreach ($affiliateOperations as $op) {
            if (preg_match('/\b'.preg_quote($op, '/').'\b/', $query)) {
                $matchedOperation = $op;
                break;
            }
        }

        if (! $matchedOperation) {
            return $next($request);
        }

        try {
            $data = $this->resolveOperation($matchedOperation, $query, $variables);

            return new JsonResponse([
                'data' => [
                    $matchedOperation => $data,
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                        'extensions' => [
                            'category' => 'affiliate',
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * Resolve the matched affiliate GraphQL operation.
     */
    protected function resolveOperation(string $operation, string $query, array $variables): mixed
    {
        switch ($operation) {
            case 'affiliateDashboard':
            case 'affiliateAccount':
                return $this->graphQLService->getDashboard();

            case 'productShareInfo':
                $productId = (int) ($variables['productId'] ?? ($variables['product_id'] ?? ($variables['id'] ?? 0)));
                if (! $productId) {
                    if (preg_match('/product(?:Id|_id|):\s*(\d+)/i', $query, $m)) {
                        $productId = (int) $m[1];
                    }
                }

                return $this->graphQLService->getProductShareInfo($productId);

            case 'affiliateTransactions':
                $page = (int) ($variables['page'] ?? 1);
                $limit = (int) ($variables['limit'] ?? 15);
                if ($page < 1) {
                    $page = 1;
                }
                if ($limit < 1) {
                    $limit = 15;
                }

                return $this->graphQLService->getTransactions($page, $limit);

            case 'affiliateCommissions':
                $page = (int) ($variables['page'] ?? 1);
                $limit = (int) ($variables['limit'] ?? 15);
                if ($page < 1) {
                    $page = 1;
                }
                if ($limit < 1) {
                    $limit = 15;
                }

                return $this->graphQLService->getCommissions($page, $limit);

            case 'affiliatePayouts':
                $page = (int) ($variables['page'] ?? 1);
                $limit = (int) ($variables['limit'] ?? 15);
                if ($page < 1) {
                    $page = 1;
                }
                if ($limit < 1) {
                    $limit = 15;
                }

                return $this->graphQLService->getPayouts($page, $limit);

            case 'registerAffiliate':
                return $this->graphQLService->registerAffiliate();

            case 'generateAffiliateLink':
                $url = $variables['url'] ?? null;
                $productId = isset($variables['productId']) ? (int) $variables['productId'] : null;

                return $this->graphQLService->generateLink($url, $productId);

            case 'updateAffiliatePayoutSettings':
                return $this->graphQLService->updatePayoutSettings($variables);

            case 'requestAffiliatePayout':
                return $this->graphQLService->requestPayout($variables);

            case 'trackAffiliateReferral':
                $code = (string) ($variables['referralCode'] ?? ($variables['referral_code'] ?? ''));
                if (! $code && preg_match('/(?:referralCode|referral_code):\s*"([^"]+)"/i', $query, $m)) {
                    $code = $m[1];
                }

                return $this->graphQLService->trackReferral($code);

            default:
                throw new \Exception("Unsupported affiliate operation: {$operation}");
        }
    }
}
