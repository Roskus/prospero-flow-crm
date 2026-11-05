<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Country;

use App\Http\Requests\CountryListRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;
use Squire\Repository;

class CountryListController
{
    /**
     * Read from the countries library source of the language, not through the Squire model,
     * because the model keeps the language of the first query of the process.
     */
    #[OAT\Get(
        path: '/country',
        summary: 'Countries accepted as country_id, with their name in the requested language',
        security: [['bearerAuth' => []]],
        tags: ['Country'],
        parameters: [
            new OAT\Parameter(name: 'lang', description: 'Language of the names; the application locale by default', in: 'query', required: false, schema: new OAT\Schema(type: 'string', enum: ['en', 'es'], example: 'es')),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Country list sorted by name',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'count', description: 'Total number of results', type: 'integer', example: 250),
                        new OAT\Property(property: 'countries', type: 'array', items: new OAT\Items(
                            properties: [
                                new OAT\Property(property: 'id', description: 'Country ISO 3166-1 alpha-2 code', type: 'string', example: 'es'),
                                new OAT\Property(property: 'name', description: 'Country name', type: 'string', example: 'España'),
                            ],
                            type: 'object'
                        )),
                    ],
                    type: 'object'
                )
            ),
            new OAT\Response(response: 422, description: 'Unsupported language'),
        ]
    )]
    public function index(CountryListRequest $request): JsonResponse
    {
        $lines = Repository::fetchData(Country::class, $request->language());
        $columns = str_getcsv((string) array_shift($lines), escape: '\\');

        $countries = collect($lines)
            ->filter(fn (string $line): bool => trim($line) !== '')
            ->map(fn (string $line): array => array_combine($columns, str_getcsv($line, escape: '\\')))
            ->map(fn (array $country): array => ['id' => $country['id'], 'name' => $country['name']])
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return response()->json(['count' => $countries->count(), 'countries' => $countries]);
    }
}
