<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPatient()
            || $user->isPsychologist()
            || $user->hasPermissionTo('reports.view');
    }

    public function view(User $user, Report $report): bool
    {
        if ($user->hasPermissionTo('reports.view')) {
            return true;
        }

        if ($report->isReporter($user)) {
            return true;
        }

        // Reported person only after admin has written on their thread.
        return $report->isReportedUser($user) && $report->hasReportedThread();
    }

    public function create(User $user): bool
    {
        return $user->isPatient() || $user->isPsychologist();
    }

    public function comment(User $user, Report $report): bool
    {
        if ($user->hasPermissionTo('reports.manage')) {
            return true;
        }

        if ($report->isClosed()) {
            return false;
        }

        if ($report->isReporter($user)) {
            return true;
        }

        return $report->isReportedUser($user) && $report->hasReportedThread();
    }

    public function update(User $user, Report $report): bool
    {
        return $user->hasPermissionTo('reports.manage');
    }
}
