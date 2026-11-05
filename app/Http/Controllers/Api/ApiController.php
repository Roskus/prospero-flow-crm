<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProspectListRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;
use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;

#[OAT\Parameter(
    parameter: 'Search',
    name: 'search',
    description: 'Text contained in the name or business name, case insensitive',
    in: 'query',
    required: false,
    schema: new OAT\Schema(type: 'string', example: 'acme', maxLength: ProspectListRequest::SEARCH_MAX_LENGTH)
)]
#[OAT\Parameter(
    parameter: 'Page',
    name: 'page',
    description: 'Page of results to return',
    in: 'query',
    required: false,
    schema: new OAT\Schema(type: 'integer', default: 1, minimum: 1)
)]
#[OAT\Parameter(
    parameter: 'PerPage',
    name: 'per_page',
    description: 'Number of results per page, at most '.self::MAX_ITEMS_PER_PAGE,
    in: 'query',
    required: false,
    schema: new OAT\Schema(type: 'integer', default: self::DEFAULT_ITEMS_PER_PAGE, maximum: self::MAX_ITEMS_PER_PAGE, minimum: 1)
)]
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
     * and adds the pagination fields next to them. The page links keep the query of the request
     * (`per_page`, filters...), so following them does not change the page size.
     *
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     */
    protected function paginatedListResponse(string $resultsKey, LengthAwarePaginator $paginator): JsonResponse
    {
        $pagination = $paginator->appends(request()->query())->toArray();
        unset($pagination['data']);

        return response()->json([
            'count' => $paginator->total(),
            $resultsKey => $paginator->items(),
            ...$pagination,
        ]);
    }
}
