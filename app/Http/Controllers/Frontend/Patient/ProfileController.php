<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


use Symfony\Component\HttpFoundation\StreamedResponse;


class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profiles) {}

    public function show(): View
    {
        $patient = $this->profiles->patientProfile(auth()->user());

        return view('frontend.patient.profile.show', compact('patient'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profiles->updatePatientProfile(
            $request->user(),
            $request->validated(),
        );

        if ($request->filled('locale')) {
            $request->session()->put('locale', $request->input('locale'));
        }

        return redirect()
            ->route('frontend.patient.profile.show')
            ->with('success', __('profile_api.updated'));
    }


    public function userDataBackup(): StreamedResponse
    {
        return $this->profiles->exportPatientData(
            request()->user()
        );
    }

    public function userDeleteConfirm(): View
    {
        return view('frontend.patient.profile.delete');
    }

    public function destroy(): RedirectResponse
    {
        $this->profiles->deletePatientAccount(
            request()->user()
        );

        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->route('frontend.login')
            ->with('success', __('profile_api.deleted'));
    }

}

