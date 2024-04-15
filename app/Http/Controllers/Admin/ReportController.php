<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Repositories\CodeRepository;
use App\Services\CodeService;
use App\Services\ReportService;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function __construct(
        private CodeService $codeService,
        private CodeRepository $codeRepository,
        private ReportService $reportService
    ) {}

    public function index(ReportRequest $reportRequest): View
    {
        $codes = $this->codeRepository->get();
        [$clickReport, $followReport, $blockReport, $visitedReport] = $this->reportService->get($reportRequest);
        dd($clickReport, $followReport, $blockReport, $visitedReport);

        return view('report.index', compact('codes', 'clickReport', 'followReport', 'blockReport', 'visitedReport'));
    }
}
