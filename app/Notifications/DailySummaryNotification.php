<?php

namespace App\Notifications;

class DailySummaryNotification
{
    public static function for(array $stats): array
    {
        //$unread = $stats['unread_messages'] ?? 0;
        $unread = (int) ($stats['unread_messages'] ?? 0);
        $pendingExchanges = (int) ($stats['pending_exchanges'] ?? 0);
        $forPsychologist = (bool) ($stats['for_psychologist'] ?? false);

        // §5.6 optional daily summary — psychologists get pending-exchange wording.
        if ($forPsychologist) {
            $count = $pendingExchanges > 0 ? $pendingExchanges : $unread;

            return [
                'type' => 'daily_summary',
                'title' => __('frontend.notifications.daily_summary_title'),
                // Old: "You have {$unread} unread message(s) waiting."
                'body' => $count > 0
                    ? __('frontend.notifications.daily_summary_psych_body', ['count' => $count])
                    : __('frontend.notifications.daily_summary_empty'),
                'data' => $stats,
            ];
        }

        return [
            'type' => 'daily_summary',
            //'title' => 'Your daily summary',
            'title' => __('frontend.notifications.daily_summary_title'),
            'body' => $unread > 0
            //  ? "You have {$unread} unread message(s) waiting."
            //: 'No new activity today.',
                ? __('frontend.notifications.daily_summary_patient_body', ['count' => $unread])
                : __('frontend.notifications.daily_summary_empty'),
            'data' => $stats,
        ];
    }
}
