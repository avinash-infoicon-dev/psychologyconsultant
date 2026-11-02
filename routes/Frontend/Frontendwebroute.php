<?php

use App\Http\Controllers\Frontend\Auth\EmailVerificationController;
use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Frontend\Auth\PasswordResetController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\Auth\TwoFactorController;
use App\Http\Controllers\Frontend\Cms\BlogController as CmsBlogController;
use App\Http\Controllers\Frontend\Cms\CategoryController as CmsCategoryController;
use App\Http\Controllers\Frontend\Cms\PageController as CmsPageController;
use App\Http\Controllers\Frontend\ContactInquiryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LocaleController;
use App\Http\Controllers\Frontend\OnboardingController;
use App\Http\Controllers\Frontend\NotificationController as FrontendNotificationController;
use App\Http\Controllers\Frontend\Patient\ConversationController;
use App\Http\Controllers\Frontend\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Frontend\Patient\MessageController;
use App\Http\Controllers\Frontend\Patient\ProfileController;
use App\Http\Controllers\Frontend\Patient\PsychologistDirectoryController;
use App\Http\Controllers\Frontend\Patient\SubscriptionController;
use App\Http\Controllers\Frontend\Psychologist\AbsenceController;
use App\Http\Controllers\Frontend\Psychologist\AvailabilityController;
use App\Http\Controllers\Frontend\Psychologist\ClinicalNoteController as FrontendClinicalNoteController;
use App\Http\Controllers\Frontend\Psychologist\DashboardController as PsychologistDashboardController;
use App\Http\Controllers\Frontend\Psychologist\PatientController as PsychologistPatientController;
use App\Http\Controllers\Frontend\Psychologist\ProfileAwardController;
use App\Http\Controllers\Frontend\Psychologist\ProfileController as PsychologistProfileController;
use App\Http\Controllers\Frontend\Psychologist\ProfileDocumentController;
use App\Http\Controllers\Frontend\Psychologist\ProfileEducationController;
use App\Http\Controllers\Frontend\Psychologist\ProfileExperienceController;
use App\Http\Controllers\Frontend\Psychologist\ProfileGalleryController;
use App\Http\Controllers\Frontend\Psychologist\ProfileSocialLinkController;
use App\Http\Controllers\Frontend\Psychologist\ResponseTemplateController;
use App\Http\Controllers\Frontend\Psychologist\TrackingController;
use App\Http\Controllers\Frontend\Speech\SpeechController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('frontend.locale');

Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/about', fn () => app(CmsPageController::class)->show('about'))->name('frontend.about');
Route::get('/services', fn () => app(CmsPageController::class)->show('services'))->name('frontend.services');
Route::get('/faq', fn () => app(CmsPageController::class)->show('faq'))->name('frontend.faq');
Route::get('/contact', fn () => app(CmsPageController::class)->show('contact'))->name('frontend.contact');
Route::get('/start', [OnboardingController::class, 'plans'])->name('frontend.onboarding.plans');
Route::get('/start/{plan}', [OnboardingController::class, 'account'])->name('frontend.onboarding.account');
Route::post('/contact-inquiries', [ContactInquiryController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('frontend.contact-inquiries.store');
Route::get('/blog', [CmsBlogController::class, 'index'])->name('frontend.blog');
Route::get('/blog/category/{slug}', [CmsBlogController::class, 'category'])->name('frontend.blog.category');
Route::get('/blog/{slug}', [CmsBlogController::class, 'show'])->name('frontend.blog.show');

Route::get('/pages/{slug}', [CmsPageController::class, 'show'])->name('frontend.cms.pages.show');
Route::get('/categories/{slug}', [CmsCategoryController::class, 'show'])->name('frontend.cms.categories.show');

Route::get('/meet-psychologists', function () {
    if (auth()->user()?->isPatient()) {
        return redirect()->route('frontend.patient.psychologists.index');
    }

    session()->put('url.intended', route('frontend.patient.psychologists.index'));

    return redirect()->route('frontend.auth.login');
})->name('frontend.meet-psychologists');

Route::get('/meet-psychologists/{professional:uuid}', function (\App\Models\Professional $professional) {
    if (auth()->user()?->isPatient()) {
        return redirect()->route('frontend.patient.psychologists.show', $professional);
    }

    session()->put('url.intended', route('frontend.patient.psychologists.show', $professional));

    return redirect()->route('frontend.auth.login');
})->name('frontend.meet-psychologists.show');

Route::get('/login', [LoginController::class, 'create'])->name('frontend.auth.login');

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('frontend.auth.register');
    Route::get('/register/email-available', [RegisterController::class, 'emailAvailable'])
        ->middleware('throttle:30,1')
        ->name('frontend.auth.register.email');
    Route::post('/register', [RegisterController::class, 'store']);
    // Account creation uses the existing /register page.
    // Route::post('/start/{plan}', [OnboardingController::class, 'store'])->name('frontend.onboarding.store');

    Route::get('/verify-email', [EmailVerificationController::class, 'show'])->name('frontend.auth.verify-email');
    Route::post('/verify-email', [EmailVerificationController::class, 'verify'])->name('frontend.auth.verify-email.submit');
    Route::post('/verify-email/resend', [EmailVerificationController::class, 'resend'])->name('frontend.auth.verify-email.resend');

    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('frontend.auth.two-factor');
    Route::post('/two-factor', [TwoFactorController::class, 'verify'])->name('frontend.auth.two-factor.submit');

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('frontend.auth.password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('frontend.auth.password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('frontend.auth.password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('frontend.auth.password.update');
});

Route::middleware('auth')->group(function () {
    Route::delete('/logout', [LoginController::class, 'destroy'])->name('frontend.auth.logout');

    Route::get('/notifications', [FrontendNotificationController::class, 'index'])->name('frontend.notifications.index');
    Route::get('/notifications/feed', [FrontendNotificationController::class, 'feed'])->name('frontend.notifications.feed');
    Route::post('/notifications/read-all', [FrontendNotificationController::class, 'markAllRead'])->name('frontend.notifications.read-all');
    Route::post('/notifications/{notification}/read', [FrontendNotificationController::class, 'markRead'])->name('frontend.notifications.read');
    Route::delete('/notifications/{notification}', [FrontendNotificationController::class, 'destroy'])->name('frontend.notifications.destroy');
    // Old: preferences were admin-only.
    // Preferences are configured in the admin panel.
    Route::put('/notifications/preferences', [FrontendNotificationController::class, 'updatePreferences'])->name('frontend.notifications.preferences.update');

    Route::middleware('role:patient')->prefix('patient')->name('frontend.patient.')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

        // Route::get('/', [ProfileController::class, 'show'])->name('show');
        // Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        // Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/export', [ProfileController::class, 'userDataBackup'])->name('profile.export');
        Route::get('/delete', [ProfileController::class, 'userDeleteConfirm'])->name('profile.delete');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');



        Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('subscription/current', [SubscriptionController::class, 'current'])->name('subscription.current');
        Route::get('subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
        Route::get('subscription/history', [SubscriptionController::class, 'history'])->name('subscription.history');
        Route::get('subscription/exchanges', [SubscriptionController::class, 'exchanges'])->name('subscription.exchanges');
        Route::get('subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::post('subscription/checkout/{plan}', [SubscriptionController::class, 'pay'])->name('subscription.pay');
        Route::post('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
        Route::post('subscription/auto-renew', [SubscriptionController::class, 'autoRenew'])->name('subscription.auto-renew');
        Route::post('subscription/retry-payment', [SubscriptionController::class, 'retryPayment'])->name('subscription.retry-payment');

        Route::get('psychologists', [PsychologistDirectoryController::class, 'index'])->name('psychologists.index');
        // Legacy binding used {psychologist:uuid}; the URL and route name remain stable for existing links.
        Route::get('psychologists/{professional:uuid}', [PsychologistDirectoryController::class, 'show'])->name('psychologists.show');

        Route::get('conversations', [ConversationController::class, 'index'])->name('conversations.index');
        Route::post('conversations', [ConversationController::class, 'store'])->name('conversations.store');
        Route::get('conversations/{conversation:uuid}', [ConversationController::class, 'show'])->name('conversations.show');
        Route::post('conversations/{conversation:uuid}/close', [ConversationController::class, 'close'])->name('conversations.close');
        Route::get('messages/unread-summary', [ConversationController::class, 'unreadSummary'])->name('messages.unread-summary');

        Route::get('conversations/{conversation:uuid}/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::post('conversations/{conversation:uuid}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('conversations/{conversation:uuid}/messages/audio', [MessageController::class, 'storeAudio'])->name('messages.store-audio');
        Route::get('messages/{message}/audio', [MessageController::class, 'streamAudio'])->name('messages.audio');
        Route::post('conversations/{conversation:uuid}/messages/read', [MessageController::class, 'markConversationRead'])->name('messages.mark-conversation-read');
        Route::patch('messages/{message}/read', [MessageController::class, 'markRead'])->name('messages.mark-read');

        Route::post('speech/transcribe', [SpeechController::class, 'transcribe'])->name('speech.transcribe');
        Route::post('relationships/request', [\App\Http\Controllers\Frontend\Patient\RelationshipController::class, 'store'])->name('relationships.store');
        Route::post('relationships/{relationship}/close', [\App\Http\Controllers\Frontend\Patient\RelationshipController::class, 'close'])->name('relationships.close');
        Route::post('relationships/{relationship}/activate', [\App\Http\Controllers\Frontend\Patient\RelationshipController::class, 'activate'])->name('relationships.activate');

        Route::get('reports', [\App\Http\Controllers\Frontend\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/create', [\App\Http\Controllers\Frontend\ReportController::class, 'create'])->name('reports.create');
        Route::post('reports', [\App\Http\Controllers\Frontend\ReportController::class, 'store'])->name('reports.store');
        Route::get('reports/{report}', [\App\Http\Controllers\Frontend\ReportController::class, 'show'])->name('reports.show');
        Route::post('reports/{report}/comments', [\App\Http\Controllers\Frontend\ReportController::class, 'comment'])->name('reports.comments.store');
    });

    Route::middleware('role:psychologist')->prefix('psychologist')->name('frontend.psychologist.')->group(function () {
        Route::get('/dashboard', [PsychologistDashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [PsychologistProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [PsychologistProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/settings', [PsychologistProfileController::class, 'updateSettings'])->name('profile.settings.update');
        Route::post('profile/submit-verification', [PsychologistProfileController::class, 'submitVerification'])->name('profile.submit-verification');

        Route::post('profile/documents', [ProfileDocumentController::class, 'store'])->name('profile.documents.store');
        Route::get('profile/documents/{document}/download', [ProfileDocumentController::class, 'download'])->name('profile.documents.download');
        Route::delete('profile/documents/{document}', [ProfileDocumentController::class, 'destroy'])->name('profile.documents.destroy');

        Route::post('profile/galleries', [ProfileGalleryController::class, 'store'])->name('profile.galleries.store');
        Route::delete('profile/galleries/{gallery}', [ProfileGalleryController::class, 'destroy'])->name('profile.galleries.destroy');

        Route::post('profile/awards', [ProfileAwardController::class, 'store'])->name('profile.awards.store');
        Route::put('profile/awards/{award}', [ProfileAwardController::class, 'update'])->name('profile.awards.update');
        Route::delete('profile/awards/{award}', [ProfileAwardController::class, 'destroy'])->name('profile.awards.destroy');

        Route::post('profile/educations', [ProfileEducationController::class, 'store'])->name('profile.educations.store');
        Route::put('profile/educations/{education}', [ProfileEducationController::class, 'update'])->name('profile.educations.update');
        Route::delete('profile/educations/{education}', [ProfileEducationController::class, 'destroy'])->name('profile.educations.destroy');

        Route::post('profile/experiences', [ProfileExperienceController::class, 'store'])->name('profile.experiences.store');
        Route::put('profile/experiences/{experience}', [ProfileExperienceController::class, 'update'])->name('profile.experiences.update');
        Route::delete('profile/experiences/{experience}', [ProfileExperienceController::class, 'destroy'])->name('profile.experiences.destroy');

        Route::post('profile/social-links', [ProfileSocialLinkController::class, 'store'])->name('profile.social-links.store');
        Route::put('profile/social-links/{socialLink}', [ProfileSocialLinkController::class, 'update'])->name('profile.social-links.update');
        Route::delete('profile/social-links/{socialLink}', [ProfileSocialLinkController::class, 'destroy'])->name('profile.social-links.destroy');

        Route::get('patients', [PsychologistPatientController::class, 'index'])->name('patients.index');
        Route::get('patients/{patient}', [PsychologistPatientController::class, 'show'])->name('patients.show');

        Route::get('patients/{patient}/clinical-notes', [FrontendClinicalNoteController::class, 'index'])->name('patients.clinical-notes.index');
        Route::get('patients/{patient}/clinical-notes/create', [FrontendClinicalNoteController::class, 'create'])->name('patients.clinical-notes.create');
        Route::post('patients/{patient}/clinical-notes', [FrontendClinicalNoteController::class, 'store'])->name('patients.clinical-notes.store');
        Route::get('clinical-notes/{clinicalNote}/edit', [FrontendClinicalNoteController::class, 'edit'])->name('clinical-notes.edit');
        Route::put('clinical-notes/{clinicalNote}', [FrontendClinicalNoteController::class, 'update'])->name('clinical-notes.update');
        Route::get('conversations/{conversation:uuid}/clinical-notes', [FrontendClinicalNoteController::class, 'forConversation'])->name('conversations.clinical-notes');

        Route::get('templates', [ResponseTemplateController::class, 'index'])->name('templates.index');
        Route::get('templates/create', [ResponseTemplateController::class, 'create'])->name('templates.create');
        Route::post('templates', [ResponseTemplateController::class, 'store'])->name('templates.store');
        Route::get('templates/{template}/edit', [ResponseTemplateController::class, 'edit'])->name('templates.edit');
        Route::put('templates/{template}', [ResponseTemplateController::class, 'update'])->name('templates.update');
        Route::delete('templates/{template}', [ResponseTemplateController::class, 'destroy'])->name('templates.destroy');

        Route::get('absences', [AbsenceController::class, 'index'])->name('absences.index');
        Route::get('absences/create', [AbsenceController::class, 'create'])->name('absences.create');
        Route::post('absences', [AbsenceController::class, 'store'])->name('absences.store');
        Route::get('absences/{absence}/edit', [AbsenceController::class, 'edit'])->name('absences.edit');
        Route::put('absences/{absence}', [AbsenceController::class, 'update'])->name('absences.update');
        Route::delete('absences/{absence}', [AbsenceController::class, 'destroy'])->name('absences.destroy');

        Route::get('availability', [AvailabilityController::class, 'index'])->name('availability.index');
        Route::post('availability', [AvailabilityController::class, 'store'])->name('availability.store');
        Route::put('availability/{availability}', [AvailabilityController::class, 'update'])->name('availability.update');
        Route::delete('availability/{availability}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');

        Route::get('tracking', [TrackingController::class, 'index'])->name('tracking.index');

        Route::get('conversations', [ConversationController::class, 'index'])->name('conversations.index');
        Route::get('conversations/{conversation:uuid}', [ConversationController::class, 'show'])->name('conversations.show');
        Route::post('conversations/{conversation:uuid}/close', [ConversationController::class, 'close'])->name('conversations.close');
        Route::get('messages/unread-summary', [ConversationController::class, 'unreadSummary'])->name('messages.unread-summary');

        Route::get('conversations/{conversation:uuid}/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::post('conversations/{conversation:uuid}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('conversations/{conversation:uuid}/messages/audio', [MessageController::class, 'storeAudio'])->name('messages.store-audio');
        Route::get('messages/{message}/audio', [MessageController::class, 'streamAudio'])->name('messages.audio');
        Route::post('conversations/{conversation:uuid}/messages/read', [MessageController::class, 'markConversationRead'])->name('messages.mark-conversation-read');
        Route::patch('messages/{message}/read', [MessageController::class, 'markRead'])->name('messages.mark-read');

        Route::post('speech/transcribe', [SpeechController::class, 'transcribe'])->name('speech.transcribe');

        Route::get('relationships', [\App\Http\Controllers\Frontend\Psychologist\RelationshipController::class, 'index'])->name('relationships.index');
        Route::post('relationships/{relationship}/accept', [\App\Http\Controllers\Frontend\Psychologist\RelationshipController::class, 'accept'])->name('relationships.accept');
        Route::post('relationships/{relationship}/reject', [\App\Http\Controllers\Frontend\Psychologist\RelationshipController::class, 'reject'])->name('relationships.reject');
        Route::post('relationships/{relationship}/close', [\App\Http\Controllers\Frontend\Psychologist\RelationshipController::class, 'close'])->name('relationships.close');

        Route::get('reports', [\App\Http\Controllers\Frontend\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/create', [\App\Http\Controllers\Frontend\ReportController::class, 'create'])->name('reports.create');
        Route::post('reports', [\App\Http\Controllers\Frontend\ReportController::class, 'store'])->name('reports.store');
        Route::get('reports/{report}', [\App\Http\Controllers\Frontend\ReportController::class, 'show'])->name('reports.show');
        Route::post('reports/{report}/comments', [\App\Http\Controllers\Frontend\ReportController::class, 'comment'])->name('reports.comments.store');
    });
});
