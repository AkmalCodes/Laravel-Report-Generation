<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Carbon\Carbon;
use PDF; // Import DomPDF facade

class InventoryController extends Controller
{
    public function getInventoryData()
    {
        $result = [];

        $descriptions = Inventory::select('Description')
            ->distinct()
            ->pluck('Description')
            ->toArray();

        // Initialize variables to track overall earliest and latest dates
        $overallEarliestDate = null;
        $overallLatestDate = null;

        foreach ($descriptions as $description) {
            // Fetch all items for the current description where SalesDate is not null
            $items = Inventory::where('Description', $description)
                ->whereNotNull('SalesDate') // Only items that are sold
                ->get(['InventoryCode', 'SalesAmount', 'CategoryCode', 'SalesDate']); // Fetch inventory code, sales amount, category, and sales date

            if ($items->isEmpty()) {
                continue;
            }

            // Collect inventory codes, category code, total sales amount, number of items sold
            $inventoryCodes = $items->pluck('InventoryCode')->toArray();
            $categoryCode = $items->pluck('CategoryCode')->first();
            $salesAmount = $items->sum('SalesAmount');
            $itemsSoldCount = $items->count();

            // Get earliest and latest sales dates for the current description
            $earliestDate = Carbon::parse($items->min('SalesDate'))->format('Y-m-d');
            $latestDate = Carbon::parse($items->max('SalesDate'))->format('Y-m-d');

            // Update overall earliest and latest dates across all descriptions
            if (is_null($overallEarliestDate) || $earliestDate < $overallEarliestDate) {
                $overallEarliestDate = $earliestDate;
            }
            if (is_null($overallLatestDate) || $latestDate > $overallLatestDate) {
                $overallLatestDate = $latestDate;
            }

            // Filter inventory codes
            $filteredInventoryCodes = array_map(function ($code) use ($categoryCode) {
                $code = preg_replace('/^[12]-/', '', $code);

                if (strpos($code, $categoryCode) === 0) {
                    $code = substr($code, strlen($categoryCode));
                }

                return $code;
            }, $inventoryCodes);

            // Add data to the result array
            $result[$description] = [
                'inventory_codes' => $filteredInventoryCodes,
                'category_code' => $categoryCode,
                'sales_amount' => $salesAmount . " RM",
                'items_sold_count' => $itemsSoldCount . " pcs",
                'earliest_date' => $earliestDate,
                'latest_date' => $latestDate,
            ];
        }

        // Return the result and the overall earliest and latest dates
        return [
            'data' => $result,
            'overallEarliestDate' => $overallEarliestDate,
            'overallLatestDate' => $overallLatestDate,
        ];
    }



    public function generatePdfReport()
    {
        // Get the processed inventory data and overall earliest/latest dates
        $inventoryDataResult = $this->getInventoryData();

        $inventoryData = $inventoryDataResult['data'];
        $reportGeneratedAtStart = $inventoryDataResult['overallEarliestDate'];
        $reportGeneratedAtEnd = $inventoryDataResult['overallLatestDate'];

        // Prepare data for the view
        $data = [
            'inventoryData' => $inventoryData,
            'report_generated_at_start' => $reportGeneratedAtStart,  // Earliest date
            'report_generated_at_end' => $reportGeneratedAtEnd,      // Latest date
        ];

        // Load the view and pass the data to it
        $pdf = PDF::loadView('pdf.inventory_report', $data);

        // Download the generated PDF
        return $pdf->download('inventory_report.pdf');
    }

    public function generatePdfReportPreview()
    {
        // Get the processed inventory data and overall earliest/latest dates
        $inventoryDataResult = $this->getInventoryData();

        $inventoryData = $inventoryDataResult['data'];
        $reportGeneratedAtStart = $inventoryDataResult['overallEarliestDate'];
        $reportGeneratedAtEnd = $inventoryDataResult['overallLatestDate'];

        // Prepare data for the view
        $data = [
            'inventoryData' => $inventoryData,
            'report_generated_at_start' => $reportGeneratedAtStart,  // Earliest date
            'report_generated_at_end' => $reportGeneratedAtEnd,      // Latest date
        ];

        // Return the view to preview in HTML
        return view('pdf.inventory_report', $data);
    }


}
