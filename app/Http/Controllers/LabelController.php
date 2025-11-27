<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;

class LabelController extends Controller
{
    /**
     * Display label printing page with all packages or filtered packages
     */
    public function index(Request $request)
    {
        $query = Package::query();

        // Filter by specific criteria if provided
        if ($request->has('mailbox_number') && !empty($request->mailbox_number)) {
            $query->where('mailbox_number', $request->mailbox_number);
        }

        if ($request->has('customer_name') && !empty($request->customer_name)) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        if ($request->has('phone_number') && !empty($request->phone_number)) {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }

        // Default to only incoming packages unless specified
        $status = $request->get('status', 'Incoming');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $packages = $query->orderBy('mailbox_number')
                          ->orderBy('customer_name')
                          ->get();

        return view('labels.print', [
            'packages' => $packages,
            'filters' => $request->all()
        ]);
    }

    /**
     * Generate label for specific package
     */
    public function generateSingle($id)
    {
        $package = Package::findOrFail($id);

        return view('labels.single', [
            'package' => $package
        ]);
    }

    /**
     * Generate PDF labels for selected packages
     */
    public function generatePdf(Request $request)
    {
        $packageIds = $request->input('package_ids', []);

        if (empty($packageIds)) {
            return redirect()->back()->with('error', 'No packages selected for printing.');
        }

        $packages = Package::whereIn('id', $packageIds)->get();

        $pdf = Pdf::loadView('labels.pdf', compact('packages'))
                  ->setPaper([0, 0, 288, 432], 'portrait') // 4x6 inches in points (72 points per inch)
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                  ]);

        return $pdf->download('storage-labels-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    /**
     * Generate single PDF label
     */
    public function generateSinglePdf($id)
    {
        $package = Package::findOrFail($id);

        $pdf = Pdf::loadView('labels.pdf-single', compact('package'))
                  ->setPaper([0, 0, 288, 432], 'portrait') // 4x6 inches in points
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                  ]);

        return $pdf->download('storage-label-' . $package->mailbox_number . '-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }
}
