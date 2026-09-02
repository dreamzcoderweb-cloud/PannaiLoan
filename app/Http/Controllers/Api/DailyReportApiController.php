<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Emicollection;
use App\Models\EmicollectionDetail;
use App\Models\MobileEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyReportApiController extends Controller
{
    /**
     * Get daily report data for authenticated employee
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function dailyReport(Request $request)
    // {
    //     try {
    //         // Validate request parameters
    //         $validator = Validator::make($request->all(), [
    //             'date' => 'nullable',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Validation failed',
    //                 'errors' => $validator->errors()
    //             ], 422);
    //         }

    //         $user = MobileEmployee::findOrFail(Auth::id());

    //         // Get the selected date or use today
    //         $selectedDate = $request->input('date') ?? now()->toDateString();
    //         $date = Carbon::parse($selectedDate);

    //         // Get EMI collections for the selected date, filtered by employee's branch, route, and collection method
    //         $emiCollections = Emicollection::with([
    //             'clientname:id,name,phone,branch_id,route_id',
    //             'loanassign:id,loan_type_id,loan_amount,branch_id,route_id,client_id',
    //             'loanassign.loan:id,loan_name',
    //             'details' => function ($query) use ($date) {
    //                 // Filter details by the selected date
    //                 $query->whereDate('due_date', $date->toDateString())
    //                     ->orWhereDate('paid_date', $date->toDateString());
    //             }
    //         ])
    //             ->where('Collect_by', 'Employee')
    //             ->where('emp_id', $user->id)
    //             ->whereHas('loanassign', function ($query) use ($user) {
    //                 // Filter by employee's branch and route
    //                 $query->where('branch_id', $user->branch_id)
    //                     ->where('route_id', $user->route_id);
    //             })
    //             ->whereHas('details', function ($query) use ($date) {
    //                 // Only collections that have details matching the date
    //                 $query->whereDate('due_date', $date->toDateString())
    //                     ->orWhereDate('paid_date', $date->toDateString());
    //             })
    //             ->get();

    //         // Calculate totals for summary
    //         $totalCollections = count($emiCollections);
    //         $totalPaidAmount = 0;
    //         $detailsSummary = [];

    //         foreach ($emiCollections as $collection) {
    //             foreach ($collection->details as $detail) {
    //                 $totalPaidAmount += $detail->paid_amount ?? 0;
    //             }

    //             // Group details by status
    //             foreach ($collection->details as $detail) {
    //                 $status = $detail->status ?? 'Pending';
    //                 if (!isset($detailsSummary[$status])) {
    //                     $detailsSummary[$status] = 0;
    //                 }
    //                 $detailsSummary[$status]++;
    //             }
    //         }

    //         // Format response
    //         $reportData = [
    //             'date' => $date->format('Y-m-d'),
    //             'date_formatted' => $date->format('d-M-Y'),
    //             'employee' => [
    //                 'id' => $user->id,
    //                 'name' => $user->name,
    //                 'phone' => $user->phone,
    //                 'branch_id' => $user->branch_id,
    //                 'route_id' => $user->route_id,
    //             ],
    //             'summary' => [
    //                 'total_collections' => $totalCollections,
    //                 'total_paid_amount' => (float) $totalPaidAmount,
    //                 'details_by_status' => $detailsSummary,
    //             ],
    //             'collections' => $this->formatCollections($emiCollections),
    //         ];

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Daily report retrieved successfully',
    //             'data' => $reportData
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An error occurred while generating the report',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    // public function dailyReport(Request $request)
    // {
    //     try {

    //         $validator = Validator::make($request->all(), [
    //             'date' => 'nullable|date',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Validation failed',
    //                 'errors' => $validator->errors()
    //             ], 422);
    //         }

    //         $user = request()->user();

    //         $selectedDate = $request->input('date') ?? now()->toDateString();
    //         $date = Carbon::parse($selectedDate);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Fetch EMI Details (Each EMI Payment Separate Row)
    //         |--------------------------------------------------------------------------
    //         */

    //         $emiDetails = EmiCollectionDetail::with([
    //             'emicollection.clientname:id,name,phone',
    //             'emicollection.loanassign.loan:id,loan_name',
    //             'emicollection.loanassign:id,loan_type_id,loan_amount,client_id'
    //         ])
    //         ->whereDate('paid_date', $date->toDateString())
    //         ->whereHas('emicollection', function ($query) use ($user) {
    //             $query->where('Collect_by', 'Employee')
    //                 ->where('emp_id', $user->id);
    //         })
    //         ->get();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Summary Calculation
    //         |--------------------------------------------------------------------------
    //         */

    //         $totalCollections = $emiDetails->count();
    //         $totalPaidAmount = $emiDetails->sum('paid_amount');

    //         $detailsSummary = [];

    //         foreach ($emiDetails as $detail) {

    //             $status = $detail->status ?? 'Pending';

    //             if (!isset($detailsSummary[$status])) {
    //                 $detailsSummary[$status] = 0;
    //             }

    //             $detailsSummary[$status]++;
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Format Collections
    //         |--------------------------------------------------------------------------
    //         */

    //         $collections = [];

    //         foreach ($emiDetails as $detail) {

    //             $collection = $detail->emicollection;
    //             $client = $collection->clientname;
    //             $loanAssign = $collection->loanassign;

    //             $collections[] = [

    //                 'collection_id' => $collection->id,

    //                 'customer' => [
    //                     'id' => $client->id ?? null,
    //                     'name' => $client->name ?? '',
    //                     'phone' => $client->phone ?? ''
    //                 ],

    //                 'loan_assign_id' => $loanAssign->id ?? null,

    //                 'loan_type' => $loanAssign->loan->loan_name ?? '',

    //                 'loan_amount' => $collection->total_payable_amount ?? 0,

    //                 'installment_no' => $detail->installment_no,

    //                 'due_date' => $detail->due_date,

    //                 'emi_amount' => $detail->emi_amount,

    //                 'paid_amount' => $detail->paid_amount,

    //                 'paid_date' => $detail->paid_date,

    //                 'remaining_payable_amount' => $detail->remaining_payable_amount,

    //                 'status' => $detail->status,

    //                 'discount' => $detail->discount ?? 0,
    //             ];
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Final Response
    //         |--------------------------------------------------------------------------
    //         */

    //         $reportData = [

    //             'date' => $date->format('Y-m-d'),

    //             'date_formatted' => $date->format('d-M-Y'),

    //             'employee' => [
    //                 'id' => $user->id,
    //                 'name' => $user->name,
    //                 'phone' => $user->phone,
    //                 'branch_id' => $user->branch_id,
    //                 'route_id' => $user->route_id,
    //             ],

    //             'summary' => [
    //                 'total_collections' => $totalCollections,
    //                 'total_paid_amount' => (float) $totalPaidAmount,
    //                 'details_by_status' => $detailsSummary,
    //             ],

    //             'collections' => $collections
    //         ];

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Daily report retrieved successfully',
    //             'data' => $reportData
    //         ], 200);

    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An error occurred while generating the report',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function dailyReport(Request $request)
{
    try {

        $validator = Validator::make($request->all(), [
            'date'  => 'nullable|date',
            'limit' => 'nullable|integer|min:1',
            'page'  => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $selectedDate = $request->input('date') ?? now()->toDateString();
        $date = Carbon::parse($selectedDate);

        $limit = $request->input('limit', 10);

        /*
        |--------------------------------------------------------------------------
        | Base Query (Reusable)
        |--------------------------------------------------------------------------
        */

        $baseQuery = EmiCollectionDetail::with([
            'emicollection.clientname:id,name,phone',
            'emicollection.loanassign.loan:id,loan_name',
            'emicollection.loanassign:id,loan_type_id,loan_amount,client_id,branch_id,route_id'
        ])
        ->whereDate('paid_date', $date->toDateString())
        ->whereHas('emicollection', function ($query) use ($user) {
            $query->where('Collect_by', 'Employee');
            if (!empty($user->branch_id)) {
                $query->whereHas('loanassign', function ($lq) use ($user) {
                    $lq->where('branch_id', $user->branch_id);
                    if (!empty($user->route_id)) {
                        $lq->where('route_id', $user->route_id);
                    }
                });
            }
        })
        ->orderBy('id', 'desc');

        /*
        |--------------------------------------------------------------------------
        | Summary (FULL DATA - Not Paginated)
        |--------------------------------------------------------------------------
        */

        $allEmiDetails = (clone $baseQuery)->get();

        $totalCollections = $allEmiDetails->count();
        $totalPaidAmount  = $allEmiDetails->sum('paid_amount');

        $detailsSummary = [];

        foreach ($allEmiDetails as $detail) {
            $status = $detail->status ?? 'Pending';
            $detailsSummary[$status] = ($detailsSummary[$status] ?? 0) + 1;
        }

        /*
        |--------------------------------------------------------------------------
        | Paginated Data
        |--------------------------------------------------------------------------
        */

        $emiDetails = $baseQuery->paginate($limit);

        /*
        |--------------------------------------------------------------------------
        | Format Collections (ONLY CURRENT PAGE)
        |--------------------------------------------------------------------------
        */

        $collections = [];

        foreach ($emiDetails->items() as $detail) {

            $collection = $detail->emicollection;
            $client = $collection->clientname ?? null;
            $loanAssign = $collection->loanassign ?? null;

            $collections[] = [
                'collection_id' => $collection->id ?? null,

                'customer' => [
                    'id' => $client->id ?? null,
                    'name' => $client->name ?? '',
                    'phone' => $client->phone ?? ''
                ],

                'loan_assign_id' => $loanAssign->id ?? null,

                'loan_type' => optional($loanAssign->loan)->loan_name ?? '',

                'loan_amount' => $collection->total_payable_amount ?? 0,

                'installment_no' => $detail->installment_no,
                'due_date' => $detail->due_date,
                'emi_amount' => $detail->emi_amount,
                'paid_amount' => $detail->paid_amount,
                'paid_date' => $detail->paid_date,
                'remaining_payable_amount' => $detail->remaining_payable_amount,
                'status' => $detail->status,
                'discount' => $detail->discount ?? 0,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Final Response
        |--------------------------------------------------------------------------
        */

        $reportData = [

            'date' => $date->format('Y-m-d'),
            'date_formatted' => $date->format('d-M-Y'),

            'employee' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'branch_id' => $user->branch_id,
                'route_id' => $user->route_id,
            ],

            'summary' => [
                'total_collections' => $totalCollections,
                'total_paid_amount' => (float) $totalPaidAmount,
                'details_by_status' => $detailsSummary,
            ],

            'pagination' => [
                'current_page' => $emiDetails->currentPage(),
                'last_page' => $emiDetails->lastPage(),
                'per_page' => $emiDetails->perPage(),
                'total' => $emiDetails->total(),
            ],

            'collections' => $collections
        ];

        return response()->json([
            'status' => true,
            'message' => 'Daily report retrieved successfully',
            'data' => $reportData
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'An error occurred while generating the report',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get total report data for all employees
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'date'  => 'nullable|date',
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $branchId = $user->branch_id ?? null;

            $selectedDate = $request->input('date') ?? now()->toDateString();
            $date = Carbon::parse($selectedDate);

            // Pagination values
            $page  = (int) $request->input('page', 1);
            $limit = (int) $request->input('limit', 10);
            $offset = ($page - 1) * $limit;

            // Total employees count for staff branch
            $employeeQuery = MobileEmployee::query();
            if (!empty($branchId)) {
                $employeeQuery->where('branch_id', $branchId);
            }

            $totalEmployees = (clone $employeeQuery)->count();

            // Paginated employees
            $employees = (clone $employeeQuery)
                ->skip($offset)
                ->take($limit)
                ->get();

            // Fetch all EMI details for selected date, filtered by staff branch
            $allEmiDetails = EmiCollectionDetail::with([
                'emicollection.clientname:id,name,phone',
                'emicollection.loanassign.loan:id,loan_name',
                'emicollection.loanassign:id,loan_type_id,loan_amount,client_id,branch_id'
            ])
            ->whereDate('paid_date', $date->toDateString())
            ->whereHas('emicollection', function ($query) use ($branchId) {
                $query->where('Collect_by', 'Employee');
                if (!empty($branchId)) {
                    $query->whereHas('loanassign', function ($lq) use ($branchId) {
                        $lq->where('branch_id', $branchId);
                    });
                }
            })
            ->orderBy('id', 'desc')
            ->get();

            // Group by employee ID
            $groupedByEmployee = $allEmiDetails->groupBy(function ($detail) {
                return $detail->emicollection->emp_id;
            });

            $employeesReports = [];
            $grandTotalCollections = 0;
            $grandTotalPaidAmount = 0;
            $grandDetailsSummary = [];

            foreach ($employees as $employee) {

                $employeeDetails = $groupedByEmployee->get($employee->id, collect());

                $totalCollections = $employeeDetails->count();
                $totalPaidAmount = $employeeDetails->sum('paid_amount');

                $detailsSummary = [];
                $collections = [];

                foreach ($employeeDetails as $detail) {

                    $status = $detail->status ?? 'Pending';

                    $detailsSummary[$status] = ($detailsSummary[$status] ?? 0) + 1;

                    $grandDetailsSummary[$status] = ($grandDetailsSummary[$status] ?? 0) + 1;

                    $collection = $detail->emicollection;
                    $client = $collection->clientname ?? null;
                    $loanAssign = $collection->loanassign ?? null;

                    $collections[] = [
                        'collection_id' => $collection->id ?? null,

                        'customer' => [
                            'id'    => $client->id ?? null,
                            'name'  => $client->name ?? '',
                            'phone' => $client->phone ?? ''
                        ],

                        'loan_assign_id' => $loanAssign->id ?? null,

                        'loan_type' => optional($loanAssign->loan)->loan_name ?? '',

                        'loan_amount' => $collection->total_payable_amount ?? 0,

                        'installment_no' => $detail->installment_no,
                        'due_date' => $detail->due_date,
                        'emi_amount' => $detail->emi_amount,
                        'paid_amount' => $detail->paid_amount,
                        'paid_date' => $detail->paid_date,
                        'remaining_payable_amount' => $detail->remaining_payable_amount,
                        'status' => $detail->status,
                        'discount' => $detail->discount ?? 0,
                    ];
                }

                $employeesReports[] = [
                    'employee' => [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'phone' => $employee->phone,
                        'branch_id' => $employee->branch_id,
                        'route_id' => $employee->route_id,
                    ],

                    'summary' => [
                        'total_collections' => $totalCollections,
                        'total_paid_amount' => (float) $totalPaidAmount,
                        'details_by_status' => $detailsSummary,
                    ],

                    'collections' => $collections
                ];

                $grandTotalCollections += $totalCollections;
                $grandTotalPaidAmount += $totalPaidAmount;
            }

            $reportData = [
                'date' => $date->format('Y-m-d'),

                'date_formatted' => $date->format('d-M-Y'),

                'pagination' => [
                    'current_page' => $page,
                    'limit' => $limit,
                    'total_employees' => $totalEmployees,
                    'total_pages' => ceil($totalEmployees / $limit),
                ],

                'grand_summary' => [
                    'total_employees' => $totalEmployees,
                    'total_collections' => $grandTotalCollections,
                    'total_paid_amount' => (float) $grandTotalPaidAmount,
                    'details_by_status' => $grandDetailsSummary,
                ],

                'employees_reports' => $employeesReports
            ];

            return response()->json([
                'status' => true,
                'message' => 'Total report retrieved successfully',
                'data' => $reportData
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while generating the total report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download daily report as PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadDailyReportPdf(Request $request)
    {
        try {
            // Validate request parameters
            $validator = Validator::make($request->all(), [
                'date' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = request()->user();

            // Get the selected date or use today
            $selectedDate = $request->input('date') ?? now()->toDateString();
            $date = Carbon::parse($selectedDate);

            // Get EMI collections for the selected date, filtered by employee's branch, route, and collection method
            $emiCollections = Emicollection::with([
                'clientname:id,name,phone,branch_id,route_id',
                'loanassign:id,loan_type_id,loan_amount,branch_id,route_id,client_id',
                'loanassign.loan:id,loan_name',
                'details' => function ($query) use ($date) {
                    $query->whereDate('due_date', $date->toDateString())
                        ->orWhereDate('paid_date', $date->toDateString());
                }
            ])
                ->where('Collect_by', 'Employee')
                ->where('emp_id', $user->id)
                ->whereHas('loanassign', function ($query) use ($user) {
                    $query->where('branch_id', $user->branch_id)
                        ->where('route_id', $user->route_id);
                })
                ->whereHas('details', function ($query) use ($date) {
                    $query->whereDate('due_date', $date->toDateString())
                        ->orWhereDate('paid_date', $date->toDateString());
                })
                ->get();

            // Calculate totals
            $totalPaidAmount = 0;


            foreach ($emiCollections as $collection) {
                foreach ($collection->details as $detail) {
                    $totalPaidAmount += $detail->paid_amount ?? 0;

                }
            }

            // Prepare data for PDF
            $reportData = [
                'date' => $date->format('Y-m-d'),
                'date_formatted' => $date->format('d-M-Y'),
                'employee' => $user,
                'total_paid_amount' => $totalPaidAmount,
                'total_collections' => count($emiCollections),
                'collections' => $this->formatCollections($emiCollections),
            ];

            // Generate PDF from view
            $pdf = Pdf::loadView('Api.daily-report-pdf', $reportData);
            $fileName = "daily_report_{$selectedDate}_{$user->id}.pdf";

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while generating the PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format collections data for response
     *
     * @param \Illuminate\Database\Eloquent\Collection $emiCollections
     * @return array
     */
    private function formatCollections($emiCollections)
    {
        return $emiCollections->map(function ($collection) {
            return [
                'id' => $collection->id,
                'customer' => [
                    'id' => $collection->clientname->id ?? null,
                    'name' => $collection->clientname->name ?? 'N/A',
                    'phone' => $collection->clientname->phone ?? 'N/A',
                ],
                'loan_assign_id' => $collection->loan_assign_id,
                'loan_type' => $collection->loanassign->loan->loan_name ?? 'N/A',
                'loan_amount' => (float) ($collection->loanassign->loan_amount ?? 0),
                'collection_type_id' => $collection->collection_type_id,
                'collection_type' => $collection->collection_type,
                'total_payable_amount' => (float) ($collection->total_payable_amount ?? 0),
                'total_collected' => (float) ($collection->total_collected ?? 0),
                'total_remaining' => (float) ($collection->total_remaining ?? 0),
                'status' => $collection->status,
                'details' => $collection->details->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'installment_no' => $detail->installment_no,
                        'installment_date' => $detail->installment_date,
                        'due_date' => $detail->due_date,
                        'emi_amount' => (float) ($detail->emi_amount ?? 0),
                        'paid_amount' => (float) ($detail->paid_amount ?? 0),
                        'paid_date' => $detail->paid_date,
                        'remaining_payable_amount' => (float) ($detail->remaining_payable_amount ?? 0),
                        'status' => $detail->status ?? 'Pending',
                        'discount' => (float) ($detail->discount ?? 0),
                    ];
                })->toArray(),
            ];
        })->toArray();
    }
}
