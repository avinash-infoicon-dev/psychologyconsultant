<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationSettingsController extends Controller
{
    public function edit(NotificationService $notifications): View
    {
        return view('backend.settings.notifications', [
            'channels' => $notifications->channelSettings(),
            'groups' => $this->groups($notifications),
        ]);
    }

    public function update(Request $request, NotificationService $notifications): RedirectResponse
    {
        $keys = array_keys($notifications->channelSettings());
        $request->validate(
            collect($keys)->mapWithKeys(fn (string $key) => [$key => ['sometimes', 'boolean']])->all()
        );

        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $request->boolean($key);
        }

        $notifications->updateChannelSettings($values);

        return redirect()
            ->route('admin.settings.notifications')
            ->with('success', __('backend.settings.notifications.updated'));
    }

    /**
     * @return list<array{group: string, label: string, email_locked: bool}>
     */
    protected function groups(NotificationService $notifications): array
    {
        // Old: hardcoded groups without apply/visible columns.
        // return [
        //     ['group' => 'new_message', 'label' => __('backend.settings.notifications.group_messages'), 'email_locked' => false],
        //     ...
        // ];

        $labels = [
            'new_message' => __('backend.settings.notifications.group_messages'),
            'subscription' => __('backend.settings.notifications.group_subscription'),
            'exchange' => __('backend.settings.notifications.group_exchange'),
            'account' => __('backend.settings.notifications.group_account'),
            'marketing' => __('backend.settings.notifications.group_marketing'),
        ];

        return array_map(function (array $row) use ($labels) {
            $row['label'] = $labels[$row['group']] ?? $row['group'];

            return $row;
        }, $notifications->preferenceGroups());
    }
}
