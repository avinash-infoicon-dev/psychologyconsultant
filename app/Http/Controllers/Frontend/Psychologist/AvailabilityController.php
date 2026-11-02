<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AvailabilityController extends Controller
{
    use ResolvesProfessional;

    public const DAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    public function index(Request $request): View
    {
        $professionalId = $this->professionalId($request->user()) ?: 0;

        $availabilities = Availability::query()
            ->where('professional_id', $professionalId)
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('start_time')
            ->get();

        $usedDays = $availabilities->pluck('day')->unique()->values()->all();

        return view('frontend.psychologist.availability.index', [
            'availabilities' => $availabilities,
            'days' => self::DAYS,
            'usedDays' => $usedDays,
            'availableDays' => array_values(array_diff(self::DAYS, $usedDays)),
        ]);
    }

    /* old commentd on 20-08-2026
    public function store(Request $request): RedirectResponse
    {
        $professionalId = $this->professionalId($request->user());
        abort_unless($professionalId, 403);

        $this->normalizeTimeInputs($request);

        $data = $request->validate([
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['required', 'string', Rule::in(self::DAYS)],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s', 'after:start_time'],
            'is_available' => ['nullable', 'boolean'],
        ], [
            'days.required' => __('frontend.availability.days_required'),
            'days.min' => __('frontend.availability.days_required'),
            'end_time.after' => __('frontend.availability.end_after_start'),
        ]);

        $days = collect($data['days'])->unique()->values();

        // One slot per day: reject if that day already exists (any start time).
        // $conflicts = Availability::query()
        //     ->where('professional_id', $professionalId)
        //     ->whereIn('day', $days->all())
        //     ->pluck('day')
        //     ->unique()
        //     ->values();

        // Previous rule: only block same day + same start_time
        $conflicts = Availability::query()
            ->where('professional_id', $professionalId)
            ->where('start_time', $data['start_time'])
            ->whereIn('day', $days->all())
            ->pluck('day');

        if ($conflicts->isNotEmpty()) {
            $labels = $conflicts
                ->map(fn (string $day) => __('frontend.availability.days.'.$day))
                ->implode(', ');

            return back()
                ->withInput()
                ->withErrors([
                    'days' => __('frontend.availability.duplicate_days', ['days' => $labels]),
                ]);
        }

        $isAvailable = $request->boolean('is_available', true);

        foreach ($days as $day) {
            Availability::query()->create([
                'professional_id' => $professionalId,
                'day' => $day,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'is_available' => $isAvailable,
            ]);
        }

        return redirect()
            ->route('frontend.psychologist.availability.index')
            ->with('success', __('frontend.availability.created'));
    } */

    public function store(Request $request): RedirectResponse
    {
        $professionalId = $this->professionalId($request->user());
        abort_unless($professionalId, 403);

        $this->normalizeTimeInputs($request);

        $data = $request->validate([
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['required', 'string', Rule::in(self::DAYS)],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s', 'after:start_time'],
            'is_available' => ['nullable', 'boolean'],
        ], [
            'days.required' => __('frontend.availability.days_required'),
            'days.min' => __('frontend.availability.days_required'),
            'end_time.after' => __('frontend.availability.end_after_start'),
        ]);

        $days = collect($data['days'])->unique()->values();

        /*
        * Check for overlapping time slots.
        *
        * Existing: 09:00 - 17:00
        * New:      11:00 - 18:00  => conflict
        *
        * Existing: 09:00 - 11:00
        * New:      11:00 - 18:00  => allowed
        */
        $conflicts = Availability::query()
            ->where('professional_id', $professionalId)
            ->whereIn('day', $days->all())
            ->where(function ($query) use ($data) {
                $query
                    ->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            })
            ->pluck('day')
            ->unique()
            ->values();

        if ($conflicts->isNotEmpty()) {
            $labels = $conflicts
                ->map(fn (string $day) => __('frontend.availability.days.' . $day))
                ->implode(', ');

            return back()
                ->withInput()
                ->withErrors([
                    'days' => __('frontend.availability.overlap_days', [
                        'days' => $labels,
                    ]),
                ]);
        }

        $isAvailable = $request->boolean('is_available', true);

        foreach ($days as $day) {
            Availability::query()->create([
                'professional_id' => $professionalId,
                'day' => $day,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'is_available' => $isAvailable,
            ]);
        }

        return redirect()
            ->route('frontend.psychologist.availability.index')
            ->with('success', __('frontend.availability.created'));
    }

    public function update(Request $request, Availability $availability): RedirectResponse
    {
        $this->assertOwns($request, $availability);

        $data = $this->validatedForUpdate($request, (int) $availability->professional_id, $availability->id);

        $availability->update([
            ...$data,
            'is_available' => $request->boolean('is_available'),
        ]);

        return redirect()
            ->route('frontend.psychologist.availability.index')
            ->with('success', __('frontend.availability.updated'));
    }

    public function destroy(Request $request, Availability $availability): RedirectResponse
    {
        $this->assertOwns($request, $availability);
        $availability->delete();

        return redirect()
            ->route('frontend.psychologist.availability.index')
            ->with('success', __('frontend.availability.deleted'));
    }

    /*protected function validatedForUpdate(Request $request, int $professionalId, int $ignoreId): array
    {
        $this->normalizeTimeInputs($request);

        return $request->validate([
            'day' => [
                'required',
                Rule::in(self::DAYS),
                // One slot per day (ignore the row being edited).
                Rule::unique('availabilities', 'day')
                    ->where(fn ($q) => $q->where('professional_id', $professionalId))
                    ->ignore($ignoreId),
            ],
            'start_time' => ['required', 'date_format:H:i:s'],
            // Previous uniqueness: day + start_time
            // 'start_time' => [
            //     'required',
            //     'date_format:H:i:s',
            //     Rule::unique('availabilities', 'start_time')
            //         ->where(fn ($q) => $q
            //             ->where('professional_id', $professionalId)
            //             ->where('day', $request->input('day')))
            //         ->ignore($ignoreId),
            // ],
            'end_time' => ['required', 'date_format:H:i:s', 'after:start_time'],
            'is_available' => ['nullable', 'boolean'],
        ], [
            'day.unique' => __('frontend.availability.day_already_exists'),
            // 'start_time.unique' => __('frontend.availability.duplicate_slot'),
            'end_time.after' => __('frontend.availability.end_after_start'),
        ]);
    } */

    protected function validatedForUpdate(
        Request $request,
        int $professionalId,
        int $ignoreId
    ): array {
        $this->normalizeTimeInputs($request);

        $data = $request->validate([
            'day' => [
                'required',
                'string',
                Rule::in(self::DAYS),
            ],
            'start_time' => [
                'required',
                'date_format:H:i:s',
            ],
            'end_time' => [
                'required',
                'date_format:H:i:s',
                'after:start_time',
            ],
            'is_available' => [
                'nullable',
                'boolean',
            ],
        ], [
            'day.required' => __('frontend.availability.day_required'),
            'end_time.after' => __('frontend.availability.end_after_start'),
        ]);

        /*
        * Check for overlapping time slots.
        *
        * Existing: 09:00 - 17:00
        * Updating to: 11:00 - 18:00 => conflict
        *
        * Existing: 09:00 - 11:00
        * Updating to: 11:00 - 18:00 => allowed
        *
        * The current availability row is excluded using $ignoreId.
        */
        $conflict = Availability::query()
            ->where('professional_id', $professionalId)
            ->where('day', $data['day'])
            ->where('id', '!=', $ignoreId)
            ->where(function ($query) use ($data) {
                $query
                    ->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'day' => __('frontend.availability.overlap_days'),
            ]);
        }

        return $data;
    }

    protected function normalizeTimeInputs(Request $request): void
    {
        foreach (['start_time', 'end_time'] as $field) {
            $value = $request->input($field);
            if (is_string($value) && preg_match('/^\d{2}:\d{2}$/', $value)) {
                $request->merge([$field => $value.':00']);
            }
        }
    }

    protected function assertOwns(Request $request, Availability $availability): void
    {
        abort_unless(
            (int) $this->professionalId($request->user()) === (int) $availability->professional_id,
            403
        );
    }
}
