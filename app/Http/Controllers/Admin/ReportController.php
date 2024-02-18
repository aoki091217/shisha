<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Repositories\CodeRepository;
use App\Repositories\ReportRepository;
use App\Services\CodeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private CodeService $codeService,
        private CodeRepository $codeRepository,
        private ReportRepository $reportRepository
    ) {}

    public function index(ReportRequest $reportRequest): View
    {
        $codes = $this->codeRepository->get();
        $clickReport = $this->reportRepository->getCodeClickCount($reportRequest);
        $followReport = $this->reportRepository->getFollowerCount($reportRequest);
        $blockReport = $this->reportRepository->getBlockCount($reportRequest);
        $usedCouponReport = $this->reportRepository->getUsedCouponCount($reportRequest);
        $visitedReport = $this->reportRepository->getVisitedCount($reportRequest);

        return view('report.index', compact('codes', 'clickReport', 'followReport', 'blockReport', 'usedCouponReport', 'visitedReport'));
    }
}
