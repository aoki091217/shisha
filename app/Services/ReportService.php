<?php

namespace App\Services;

use App\Http\Requests\CodeReportRequest;
use App\Http\Requests\ReportRequest;
use App\Models\Shop;
use App\Repositories\ReportRepository;
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        private ReportRepository $reportRepository
    ) {}

    public function get(ReportRequest $reportRequest): array
    {
        $shops = Shop::get();

        return [
            $this->reportRepository->getCodeClickCount($reportRequest, $shops),
            $this->reportRepository->getFollowerCount($reportRequest, $shops),
            $this->reportRepository->getBlockCount($reportRequest, $shops),
            //$this->reportRepository->getUsedCouponCount($reportRequest),
            $this->reportRepository->getVisitedCount($reportRequest, $shops),
            $this->reportRepository->getVisitRate($reportRequest, $shops)
        ];
    }
}
