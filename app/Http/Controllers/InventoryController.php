<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function getInventoryData()
    {

        $result = [];

        $descriptions = Inventory::select('Description')
            ->distinct()
            ->pluck('Description')
            ->toArray();

        // Step 3: Loop through each description and fetch relevant data
        foreach ($descriptions as $description) {
            // Fetch all items for the current description where SalesDate is not null
            $items = Inventory::where('Description', $description)
                ->whereNotNull('SalesDate') // Only items that are sold
                ->get(['InventoryCode', 'SalesAmount' ,'CategoryCode']); // Fetch inventory code and total cost

            // Collect inventory codes and total cost for this description
            $inventoryCodes = $items->pluck('InventoryCode')->toArray(); // Get inventory codes
            $categoryCode = $items->pluck('CategoryCode')->first(); // Get inventory codes
            $SalesAmount = $items->sum('SalesAmount'); // Sum the total cost
            $itemsSoldCount = $items->count(); // Get the number of items

            $filteredInventoryCodes = array_map(function ($code) use ($categoryCode) {
                // Remove '1-' or '2-' from the beginning of the code
                $code = preg_replace('/^[12]-/', '', $code);
        
                // Remove the category code (if present) from the beginning of the code
                if (strpos($code, $categoryCode) === 0) {
                    $code = substr($code, strlen($categoryCode));
                }
        
                return $code;
            }, $inventoryCodes);

            $result[$description] = [
                'inventory_codes' => $filteredInventoryCodes,
                'CategoryCode' => $categoryCode,
                'SalesAmount' => $SalesAmount . " RM",
                'no. of items sold' => $itemsSoldCount . " pcs"
            ];
        }

        // Dump the data or return it in a response
        // return response()->json($result);
        dd($result);
    }

}
