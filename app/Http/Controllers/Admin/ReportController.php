<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerReportRequest;
use App\Http\Requests\CodeReportRequest;
use App\Http\Requests\ReportRequest;
use App\Models\Code;
use App\Repositories\CodeRepository;
use App\Repositories\ReportRepository;
use App\Repositories\ShopRepository;
use App\Services\CodeService;
use App\Services\ReportService;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function __construct(
        private CodeService      $codeService,
        private CodeRepository   $codeRepository,
        private ShopRepository   $shopRepository,
        private ReportService    $reportService,
        private ReportRepository $reportRepository,
    )
    {
    }

    public function index(ReportRequest $reportRequest): View
    {
        $codes = $this->codeRepository->get();
        [$clickReport, $followReport, $blockReport, $visitedCountReport, $visitRateReport] = $this->reportService->get($reportRequest);

        return view('report.index', compact('codes', 'clickReport', 'followReport', 'blockReport', 'visitedCountReport', 'visitRateReport'));
    }

    public function code(CodeReportRequest $reportRequest): View
    {
        $shops = $this->shopRepository->getShops()->keyBy('shop_id');
        $shop_ids = $shops->keys()->toArray();

        // TODO: SQLでフィルタしたい
        $codes = $this->codeRepository->get()
            ->filter(fn(Code $code) => $code->getKind() === 1 && in_array($code->shop_id, $shop_ids));

	$code_ids = $codes->pluck('code_id')->toArray();
        $filters = [
            'startDate' => $reportRequest->startDate(),
            'endDate' => $reportRequest->endDate(),
            'shopIds' => empty($reportRequest->shopIds()) ? $shop_ids :array_intersect($reportRequest->shopIds(), $shop_ids),
            'codeIds' => empty($reportRequest->codeIds()) ? $code_ids : array_intersect($reportRequest->codeIds(), $code_ids),
        ];
        $report = $this->reportRepository->getCodeReport($filters);

        return view('report.code', compact('shops', 'codes', 'report'));
    }

    public function customer(CustomerReportRequest $reportRequest): View
    {
        $shops = $this->shopRepository->getShops()->keyBy('shop_id');
	$shop_ids = $shops->keys()->toArray();

        // TODO: SQLでフィルタしたい
        $filters = [
            'startDate' => $reportRequest->startDate(),
            'endDate' => $reportRequest->endDate(),
            'shopIds' => empty($reportRequest->shopIds()) ? $shop_ids :array_intersect($reportRequest->shopIds(), $shop_ids),
	];

        $report = $this->reportRepository->getCustomerReport($filters);

        return view('report.customer', compact('shops', 'report'));
    }
}
