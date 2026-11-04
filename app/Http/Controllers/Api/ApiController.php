<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;

class ApiController extends Controller
{
    /**
     * Results per page of a paginated list when the request does not send `per_page`.
     */
    public const int DEFAULT_ITEMS_PER_PAGE = 20;

    /**
     * Most results per page a paginated list returns, whatever `per_page` asks for.
     */
    public const int MAX_ITEMS_PER_PAGE = 500;

    protected ?User $user = null;

    public function __construct()
    {
        try {
            $this->user = Auth::user();
        } catch (UserNotDefinedException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Results per page of a paginated list from the `per_page` query, between 1 and the maximum.
     */
    protected function itemsPerPage(Request $request): int
    {
        return max(1, min($request->integer('per_page', self::DEFAULT_ITEMS_PER_PAGE), self::MAX_ITEMS_PER_PAGE));
    }

    /**
     * Keeps the list response of the API, `count` (total results) and the results under `$resultsKey`,
     * and adds the pagination fields next to them.
     *
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     */
    protected function paginatedListResponse(string $resultsKey, LengthAwarePaginator $paginator): JsonResponse
    {
        $pagination = $paginator->toArray();
        unset($pagination['data']);

        return response()->json([
            'count' => $paginator->total(),
            $resultsKey => $paginator->items(),
            ...$pagination,
        ]);
    }
}
