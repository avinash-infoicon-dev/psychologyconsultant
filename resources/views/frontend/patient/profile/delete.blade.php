@extends('frontend.layouts.dashboard')

{{--@section('title', __('profile_api.web.delete_account'))
@section('page_title', __('profile_api.web.delete_account'))  --}}

@section('title', __('frontend.delete_account.delete_my_account'))
@section('page_title', __('frontend.delete_account.delete_my_account'))


@section('content')

<div class="fe-cms-card">

    <div class="text-center">

        <div class="mb-3">
            <i class="bi bi-exclamation-triangle-fill text-danger"
               style="font-size: 48px;"></i>
        </div>

        {{--<h2 class="text-danger mb-3"> Delete Your Account?</h2>--}}
        <h2>{{ __('frontend.delete_account.title') }}</h2>

        {{--<p>Are you sure you want to delete your account?</p>--}}
        <p>{{ __('frontend.delete_account.confirmation') }}</p>

        <div class="alert alert-danger text-start">
            {{--<strong>Warning:</strong>--}}
            <strong>{{ __('frontend.delete_account.warning') }}</strong>

            <ul class="mb-0 mt-2">
                {{--<li>Your account will be deleted.</li>
                <li>Your profile and personal information will be removed.</li>
                <li>You will no longer be able to access your account.</li>
                <li>This action cannot be undone.</li>
                <li>You may not be able to retrieve your data after deletion.</li>--}}

                <li>{{ __('frontend.delete_account.account_deleted') }}</li>
                <li>{{ __('frontend.delete_account.profile_removed') }}</li>
                <li>{{ __('frontend.delete_account.no_access') }}</li>
                <li>{{ __('frontend.delete_account.cannot_undo') }}</li>
                <li>{{ __('frontend.delete_account.data_not_recoverable') }}</li>
            </ul>
        </div>

        <div class="mt-4">
            <a href="{{ route('frontend.patient.profile.show') }}" class="fe-btn fe-btn--secondary">{{ __('frontend.delete_account.cancel') }} </a>
            <button type="button" class="fe-btn fe-btn--danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal"> <i class="bi bi-trash"></i> {{ __('frontend.delete_account.delete_my_account') }}</button>
        </div>
    </div>

</div>


{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i>{{ __('frontend.delete_account.confirm_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{--<p class="mb-3">Are you absolutely sure you want to delete your account? </p>--}}
                <p class="mb-3">{{ __('frontend.delete_account.absolutely_sure') }} </p>
                <div class="alert alert-warning"> 
                    {{--<strong>This action cannot be undone.</strong>
                    <p class="mb-0 mt-2">Once your account is deleted, you will lose access to your account and your personal data may not be recoverable.</p>--}}
                    <strong>{{ __('frontend.delete_account.action_cannot_undo') }}</strong>
                    <p class="mb-0 mt-2">{{ __('frontend.delete_account.data_loss_warning') }}</p>
                </div>
                {{--<p class="text-muted mb-0"> Please confirm that you want to permanently delete your account. </p>--}}
                 <p class="text-muted mb-0"> {{ __('frontend.delete_account.permanent_delete_confirmation') }} </p>
            </div>

            <div class="modal-footer border-0">

                <button type="button" class="fe-btn fe-btn--secondary" data-bs-dismiss="modal">
                    {{--Cancel--}}
                    {{ __('frontend.delete_account.cancel') }}
                    </button>

                <form action="{{ route('frontend.patient.profile.destroy') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="fe-btn fe-btn--danger"> <i class="bi bi-trash"></i> 
                        {{--Yes, Delete My Account--}}
                        {{ __('frontend.delete_account.yes_delete_account') }}
                    </button>
                </form>

            </div>

        </div>

    </div>
</div>

@endsection