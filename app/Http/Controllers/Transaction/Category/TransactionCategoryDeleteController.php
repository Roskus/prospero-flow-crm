<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transaction\Category;

use App\Models\Transaction\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionCategoryDeleteController
{
    public function delete(Request $request, int $id): JsonResponse
    {
        $category = Category::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
