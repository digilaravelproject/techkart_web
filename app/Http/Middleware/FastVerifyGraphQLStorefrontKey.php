<?php

namespace App\Http\Middleware;

use Webkul\BagistoApi\Http\Middleware\VerifyGraphQLStorefrontKey;

class FastVerifyGraphQLStorefrontKey extends VerifyGraphQLStorefrontKey
{
    /**
     * In-memory cache for evaluated operation attributes.
     */
    protected static array $operationAttributeCache = [];

    /**
     * Check if a GraphQL operation has authentication attributes with memoization.
     */
    protected function checkOperationAttributes(?string $operationName): ?bool
    {
        if (! $operationName) {
            return null;
        }

        if (array_key_exists($operationName, self::$operationAttributeCache)) {
            return self::$operationAttributeCache[$operationName];
        }

        return self::$operationAttributeCache[$operationName] = parent::checkOperationAttributes($operationName);
    }
}
