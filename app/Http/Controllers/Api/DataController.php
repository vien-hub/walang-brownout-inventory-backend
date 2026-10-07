<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Stores the system data the React pages use (inventory, alerts,
 * transactions, FIFO batches, purchase orders, reports) in the database.
 */
class DataController extends Controller
{
    private const KEYS = [
        'inventory_db',
        'alerts_db',
        'transaction_records_db',
        'fifo_batches_db',
        'purchase_orders_db',
        'generated_reports_db',
    ];

    private function checkKey(string $key): void
    {
        abort_unless(in_array($key, self::KEYS, true), 404, 'Unknown data key.');
    }

    public function index(): JsonResponse
    {
        $rows = AppData::whereIn('data_key', self::KEYS)->pluck('value', 'data_key');

        return response()->json(['data' => (object) $rows->toArray()]);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $this->checkKey($key);

        $request->validate(['value' => 'required|string']);

        AppData::updateOrCreate(['data_key' => $key], ['value' => $request->input('value')]);

        return response()->json(['saved' => $key]);
    }

    public function destroy(string $key): JsonResponse
    {
        $this->checkKey($key);

        AppData::where('data_key', $key)->delete();

        return response()->json(['deleted' => $key]);
    }
}
