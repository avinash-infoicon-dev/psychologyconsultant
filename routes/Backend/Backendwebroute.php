<?php

use App\Http\Controllers\Backend\AdminLoginController;
use App\Http\Controllers\Backend\Cms\CategoryController as CmsCategoryController;
use App\Http\Controllers\Backend\Cms\MenuController as CmsMenuController;
use App\Http\Controllers\Backend\Cms\HomeSliderController as CmsHomeSliderController;
use App\Http\Controllers\Backend\Cms\TeamMemberController as CmsTeamMemberController;
use App\Http\Controllers\Backend\Cms\TestimonialController as CmsTestimonialController;
use App\Http\Controllers\Backend\Cms\FaqController as CmsFaqController;
use App\Http\Controllers\Backend\Cms\BlogCategoryController as CmsBlogCategoryController;
use App\Http\Controllers\Backend\Cms\BlogPostController as CmsBlogPostController;
use App\Http\Controllers\Backend\Cms\PageController as CmsPageController;
use App\Http\Controllers\Backend\Cms\HomeContentController as CmsHomeContentController;
use App\Http\Controllers\Backend\Cms\ServicesController as CmsServicesController;
use App\Http\Controllers\Backend\Cms\SiteSettingController as CmsSiteSettingController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\LocaleController;
use App\Http\Controllers\Backend\LogsController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\PlanController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\PatientController;
use App\Http\Controllers\Backend\PatientExchangeController;
use App\Http\Controllers\Backend\PsychologistController;
use App\Http\Controllers\Backend\ReportController as BackendReportController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SecuritySettingsController;
use App\Http\Controllers\Backend\MessagingSettingsController;
use App\Http\Controllers\Backend\NotificationController as BackendNotificationController;
use App\Http\Controllers\Backend\NotificationSettingsController;
use App\Http\Controllers\Backend\SpecializationController;
use App\Http\Controllers\Backend\SubscriptionController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

$adminPrefix = 'admin';

Route::prefix($adminPrefix)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/', [AdminLoginController::class, 'index'])->name('admin');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login');
    });

    Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('admin.locale');

    Route::middleware(['auth', 'backend.access'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/notifications', [BackendNotificationController::class, 'index'])->name('admin.notifications.index');
        Route::get('/notifications/feed', [BackendNotificationController::class, 'feed'])->name('admin.notifications.feed');
        Route::post('/notifications/read-all', [BackendNotificationController::class, 'markAllRead'])->name('admin.notifications.read-all');
        Route::post('/notifications/{notification}/read', [BackendNotificationController::class, 'markRead'])->name('admin.notifications.read');

        Route::middleware('permission:reports.view')->group(function () {
            Route::get('/reports', [BackendReportController::class, 'index'])->name('admin.reports.index');
            Route::get('/reports/{report}', [BackendReportController::class, 'show'])->name('admin.reports.show');
        });
        Route::middleware('permission:reports.manage')->group(function () {
            Route::patch('/reports/{report}', [BackendReportController::class, 'update'])->name('admin.reports.update');
            Route::post('/reports/{report}/comments', [BackendReportController::class, 'comment'])->name('admin.reports.comments.store');
        });

        Route::middleware('role:superadmin,admin')->group(function () {
            Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile.show');
            Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
            Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');
        });

        Route::middleware('permission:users.view')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        });
        Route::middleware('permission:users.create')->group(function () {
            Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
            Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        });
        Route::middleware('permission:users.edit')->group(function () {
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
            Route::post('/users/{user}/two-factor/toggle', [UserController::class, 'toggleTwoFactor'])->name('admin.users.two-factor.toggle');
        });
        Route::middleware('permission:users.delete')->group(function () {
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        });

        Route::middleware('permission:psychologists.view')->group(function () {
            Route::get('/psychologists', [PsychologistController::class, 'index'])->name('admin.psychologists.index');
            Route::get('/psychologists/verifications', [PsychologistController::class, 'verifications'])->name('admin.psychologists.verifications.index');
            Route::get('/psychologists/{psychologist}/verification', [PsychologistController::class, 'verification'])->name('admin.psychologists.verification.show');
            Route::get('/psychologists/{psychologist}', [PsychologistController::class, 'show'])->name('admin.psychologists.show');
        });
        Route::middleware('permission:psychologists.verify')->group(function () {
            // Old boolean approve/revoke:
            // Route::post('/psychologists/{psychologist}/approve', [PsychologistController::class, 'approve'])->name('admin.psychologists.approve');
            // Route::post('/psychologists/{psychologist}/revoke', [PsychologistController::class, 'revoke'])->name('admin.psychologists.revoke');
            Route::post('/psychologists/{psychologist}/start-review', [PsychologistController::class, 'startReview'])->name('admin.psychologists.start-review');
            Route::post('/psychologists/{psychologist}/verify', [PsychologistController::class, 'verify'])->name('admin.psychologists.verify');
            Route::post('/psychologists/{psychologist}/reject', [PsychologistController::class, 'reject'])->name('admin.psychologists.reject');
        });
        Route::middleware('permission:psychologists.suspend')->group(function () {
            Route::post('/psychologists/{psychologist}/suspend', [PsychologistController::class, 'suspend'])->name('admin.psychologists.suspend');
            Route::post('/psychologists/{psychologist}/restore', [PsychologistController::class, 'restore'])->name('admin.psychologists.restore');
        });

        Route::middleware('permission:patients.view')->group(function () {
            Route::get('/patients', [PatientController::class, 'index'])->name('admin.patients.index');
            Route::get('/patients/subscriptions', [PatientController::class, 'subscriptionsIndex'])->name('admin.patients.subscriptions.index');
            Route::get('/patients/conversations', [PatientController::class, 'conversationsIndex'])->name('admin.patients.conversations.index');
            Route::get('/patients/additional-exchanges', [PatientController::class, 'exchangesIndex'])->name('admin.patients.exchanges.index');
            Route::get('/patients/clinical-notes', [PatientController::class, 'notesIndex'])->name('admin.patients.notes.index');
            Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('admin.patients.show');
            Route::get('/patients/{patient}/subscriptions', [PatientController::class, 'subscriptions'])->name('admin.patients.subscriptions');
            Route::get('/patients/{patient}/conversations', [PatientController::class, 'conversations'])->name('admin.patients.conversations');
            Route::get('/patients/{patient}/exchanges', [PatientExchangeController::class, 'show'])->name('admin.patients.exchanges');
            Route::get('/patients/{patient}/clinical-notes', [PatientController::class, 'notes'])->name('admin.patients.notes');
        });
        Route::middleware('permission:subscriptions.adjust')->group(function () {
            Route::post('/patients/{patient}/exchanges/add', [PatientExchangeController::class, 'add'])->name('admin.patients.exchanges.add');
            Route::post('/patients/{patient}/exchanges/adjust', [PatientExchangeController::class, 'adjust'])->name('admin.patients.exchanges.adjust');
        });

        Route::middleware('permission:specializations.view')->group(function () {
            Route::get('/specializations', [SpecializationController::class, 'index'])->name('admin.specializations.index');
        });
        Route::middleware('permission:specializations.create')->group(function () {
            Route::get('/specializations/create', [SpecializationController::class, 'create'])->name('admin.specializations.create');
            Route::post('/specializations', [SpecializationController::class, 'store'])->name('admin.specializations.store');
        });
        Route::middleware('permission:specializations.edit')->group(function () {
            Route::get('/specializations/{specialization}/edit', [SpecializationController::class, 'edit'])->name('admin.specializations.edit');
            Route::put('/specializations/{specialization}', [SpecializationController::class, 'update'])->name('admin.specializations.update');
            Route::patch('/specializations/{specialization}/toggle-status', [SpecializationController::class, 'toggleStatus'])->name('admin.specializations.toggle-status');
        });
        Route::middleware('permission:specializations.delete')->group(function () {
            Route::post('/specializations/{specialization}/archive', [SpecializationController::class, 'archive'])->name('admin.specializations.archive');
            // Hard delete kept for the route name; the action archives instead.
            Route::delete('/specializations/{specialization}', [SpecializationController::class, 'destroy'])->name('admin.specializations.destroy');
        });

        Route::middleware('permission:settings.view')->group(function () {
            Route::get('/settings/security', [SecuritySettingsController::class, 'edit'])->name('admin.settings.security');
            Route::get('/settings/messaging', [MessagingSettingsController::class, 'edit'])->name('admin.settings.messaging');
            Route::get('/settings/notifications', [NotificationSettingsController::class, 'edit'])->name('admin.settings.notifications');
        });
        Route::middleware('permission:settings.edit')->group(function () {
            Route::put('/settings/security', [SecuritySettingsController::class, 'update'])->name('admin.settings.security.update');
            Route::put('/settings/messaging', [MessagingSettingsController::class, 'update'])->name('admin.settings.messaging.update');
            Route::put('/settings/notifications', [NotificationSettingsController::class, 'update'])->name('admin.settings.notifications.update');
        });

        Route::middleware('permission:plans.view')->group(function () {
            Route::get('/plans', [PlanController::class, 'index'])->name('admin.plans.index');
        });
        Route::middleware('permission:plans.create')->group(function () {
            Route::get('/plans/create', [PlanController::class, 'create'])->name('admin.plans.create');
            Route::post('/plans', [PlanController::class, 'store'])->name('admin.plans.store');
        });
        Route::middleware('permission:plans.edit')->group(function () {
            Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
            Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('admin.plans.update');
        });
        Route::middleware('permission:plans.delete')->group(function () {
            Route::post('/plans/{plan}/archive', [PlanController::class, 'archive'])->name('admin.plans.archive');
            Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('admin.plans.destroy');
        });

        Route::middleware('permission:subscriptions.view')->group(function () {
            Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
        });

        Route::middleware('permission:cms.site.view')->group(function () {
            Route::get('/cms/site', [CmsSiteSettingController::class, 'edit'])->name('admin.cms.site.edit');
            Route::get('/cms/login', [CmsSiteSettingController::class, 'login'])->name('admin.cms.login.edit');
            Route::get('/cms/register', [CmsSiteSettingController::class, 'register'])->name('admin.cms.register.edit');
        });
        Route::middleware('permission:cms.site.edit')->group(function () {
            Route::put('/cms/site', [CmsSiteSettingController::class, 'update'])->name('admin.cms.site.update');
            Route::put('/cms/login', [CmsSiteSettingController::class, 'updateLogin'])->name('admin.cms.login.update');
            Route::put('/cms/register', [CmsSiteSettingController::class, 'updateRegister'])->name('admin.cms.register.update');
        });

        Route::middleware('permission:cms.categories.view')->group(function () {
            Route::get('/cms/categories', [CmsCategoryController::class, 'index'])->name('admin.cms.categories.index');
        });
        Route::middleware('permission:cms.categories.create')->group(function () {
            Route::get('/cms/categories/create', [CmsCategoryController::class, 'create'])->name('admin.cms.categories.create');
            Route::post('/cms/categories', [CmsCategoryController::class, 'store'])->name('admin.cms.categories.store');
        });
        Route::middleware('permission:cms.categories.edit')->group(function () {
            Route::get('/cms/categories/{category}/edit', [CmsCategoryController::class, 'edit'])->name('admin.cms.categories.edit');
            Route::put('/cms/categories/{category}', [CmsCategoryController::class, 'update'])->name('admin.cms.categories.update');
        });
        Route::middleware('permission:cms.categories.delete')->group(function () {
            Route::delete('/cms/categories/{category}', [CmsCategoryController::class, 'destroy'])->name('admin.cms.categories.destroy');
        });

        Route::middleware('permission:cms.pages.view')->group(function () {
            Route::get('/cms/pages', [CmsPageController::class, 'index'])->name('admin.cms.pages.index');
            Route::get('/cms/home', [CmsHomeContentController::class, 'edit'])->name('admin.cms.home.edit');
            Route::get('/cms/services', [CmsServicesController::class, 'edit'])->name('admin.cms.services.edit');
            Route::get('/cms/pages/{page}/sections', [CmsPageController::class, 'sections'])->name('admin.cms.pages.sections');
        });
        Route::middleware('permission:cms.pages.create')->group(function () {
            Route::get('/cms/pages/create', [CmsPageController::class, 'create'])->name('admin.cms.pages.create');
            Route::post('/cms/pages', [CmsPageController::class, 'store'])->name('admin.cms.pages.store');
        });
        Route::middleware('permission:cms.pages.edit')->group(function () {
            Route::get('/cms/pages/{page}/edit', [CmsPageController::class, 'edit'])->name('admin.cms.pages.edit');
            Route::put('/cms/pages/{page}', [CmsPageController::class, 'update'])->name('admin.cms.pages.update');
            Route::put('/cms/home', [CmsHomeContentController::class, 'update'])->name('admin.cms.home.update');
            Route::put('/cms/services', [CmsServicesController::class, 'update'])->name('admin.cms.services.update');
        });
        Route::middleware('permission:cms.pages.delete')->group(function () {
            Route::delete('/cms/pages/{page}', [CmsPageController::class, 'destroy'])->name('admin.cms.pages.destroy');
        });

        Route::middleware('permission:cms.sliders.view')->group(function () {
            Route::get('/cms/sliders', [CmsHomeSliderController::class, 'index'])->name('admin.cms.sliders.index');
        });
        Route::middleware('permission:cms.sliders.create')->group(function () {
            Route::get('/cms/sliders/create', [CmsHomeSliderController::class, 'create'])->name('admin.cms.sliders.create');
            Route::post('/cms/sliders', [CmsHomeSliderController::class, 'store'])->name('admin.cms.sliders.store');
        });
        Route::middleware('permission:cms.sliders.edit')->group(function () {
            Route::get('/cms/sliders/{slider}/edit', [CmsHomeSliderController::class, 'edit'])->name('admin.cms.sliders.edit');
            Route::put('/cms/sliders/{slider}', [CmsHomeSliderController::class, 'update'])->name('admin.cms.sliders.update');
        });
        Route::middleware('permission:cms.sliders.delete')->group(function () {
            Route::delete('/cms/sliders/{slider}', [CmsHomeSliderController::class, 'destroy'])->name('admin.cms.sliders.destroy');
        });

        Route::middleware('permission:cms.testimonials.view')->group(function () {
            Route::get('/cms/testimonials', [CmsTestimonialController::class, 'index'])->name('admin.cms.testimonials.index');
        });
        Route::middleware('permission:cms.testimonials.create')->group(function () {
            Route::get('/cms/testimonials/create', [CmsTestimonialController::class, 'create'])->name('admin.cms.testimonials.create');
            Route::post('/cms/testimonials', [CmsTestimonialController::class, 'store'])->name('admin.cms.testimonials.store');
        });
        Route::middleware('permission:cms.testimonials.edit')->group(function () {
            Route::get('/cms/testimonials/{testimonial}/edit', [CmsTestimonialController::class, 'edit'])->name('admin.cms.testimonials.edit');
            Route::put('/cms/testimonials/{testimonial}', [CmsTestimonialController::class, 'update'])->name('admin.cms.testimonials.update');
        });
        Route::middleware('permission:cms.testimonials.delete')->group(function () {
            Route::delete('/cms/testimonials/{testimonial}', [CmsTestimonialController::class, 'destroy'])->name('admin.cms.testimonials.destroy');
        });

        Route::middleware('permission:cms.team.view')->group(function () {
            Route::get('/cms/team', [CmsTeamMemberController::class, 'index'])->name('admin.cms.team.index');
        });
        Route::middleware('permission:cms.team.create')->group(function () {
            Route::get('/cms/team/create', [CmsTeamMemberController::class, 'create'])->name('admin.cms.team.create');
            Route::post('/cms/team', [CmsTeamMemberController::class, 'store'])->name('admin.cms.team.store');
        });
        Route::middleware('permission:cms.team.edit')->group(function () {
            Route::get('/cms/team/{teamMember}/edit', [CmsTeamMemberController::class, 'edit'])->name('admin.cms.team.edit');
            Route::put('/cms/team/{teamMember}', [CmsTeamMemberController::class, 'update'])->name('admin.cms.team.update');
        });
        Route::middleware('permission:cms.team.delete')->group(function () {
            Route::delete('/cms/team/{teamMember}', [CmsTeamMemberController::class, 'destroy'])->name('admin.cms.team.destroy');
        });

        Route::middleware('permission:cms.faqs.view')->group(function () {
            Route::get('/cms/faqs', [CmsFaqController::class, 'index'])->name('admin.cms.faqs.index');
        });
        Route::middleware('permission:cms.faqs.create')->group(function () {
            Route::get('/cms/faqs/create', [CmsFaqController::class, 'create'])->name('admin.cms.faqs.create');
            Route::post('/cms/faqs', [CmsFaqController::class, 'store'])->name('admin.cms.faqs.store');
        });
        Route::middleware('permission:cms.faqs.edit')->group(function () {
            Route::get('/cms/faqs/{faq}/edit', [CmsFaqController::class, 'edit'])->name('admin.cms.faqs.edit');
            Route::put('/cms/faqs/{faq}', [CmsFaqController::class, 'update'])->name('admin.cms.faqs.update');
        });
        Route::middleware('permission:cms.faqs.delete')->group(function () {
            Route::delete('/cms/faqs/{faq}', [CmsFaqController::class, 'destroy'])->name('admin.cms.faqs.destroy');
        });

        Route::middleware('permission:cms.blog.categories.view')->group(function () {
            Route::get('/blog/categories', [CmsBlogCategoryController::class, 'index'])->name('admin.blog.categories.index');
        });
        Route::middleware('permission:cms.blog.categories.create')->group(function () {
            Route::get('/blog/categories/create', [CmsBlogCategoryController::class, 'create'])->name('admin.blog.categories.create');
            Route::post('/blog/categories', [CmsBlogCategoryController::class, 'store'])->name('admin.blog.categories.store');
        });
        Route::middleware('permission:cms.blog.categories.edit')->group(function () {
            Route::get('/blog/categories/{blogCategory}/edit', [CmsBlogCategoryController::class, 'edit'])->name('admin.blog.categories.edit');
            Route::put('/blog/categories/{blogCategory}', [CmsBlogCategoryController::class, 'update'])->name('admin.blog.categories.update');
        });
        Route::middleware('permission:cms.blog.categories.delete')->group(function () {
            Route::delete('/blog/categories/{blogCategory}', [CmsBlogCategoryController::class, 'destroy'])->name('admin.blog.categories.destroy');
        });

        Route::middleware('permission:cms.blog.posts.view')->group(function () {
            Route::get('/blog/posts', [CmsBlogPostController::class, 'index'])->name('admin.blog.posts.index');
        });
        Route::middleware('permission:cms.blog.posts.create')->group(function () {
            Route::get('/blog/posts/create', [CmsBlogPostController::class, 'create'])->name('admin.blog.posts.create');
            Route::post('/blog/posts', [CmsBlogPostController::class, 'store'])->name('admin.blog.posts.store');
        });
        Route::middleware('permission:cms.blog.posts.edit')->group(function () {
            Route::get('/blog/posts/{blogPost}/edit', [CmsBlogPostController::class, 'edit'])->name('admin.blog.posts.edit');
            Route::put('/blog/posts/{blogPost}', [CmsBlogPostController::class, 'update'])->name('admin.blog.posts.update');
        });
        Route::middleware('permission:cms.blog.posts.delete')->group(function () {
            Route::delete('/blog/posts/{blogPost}', [CmsBlogPostController::class, 'destroy'])->name('admin.blog.posts.destroy');
        });

        Route::middleware('permission:cms.menus.view')->group(function () {
            Route::get('/cms/menus', [CmsMenuController::class, 'index'])->name('admin.cms.menus.index');
        });
        Route::middleware('permission:cms.menus.create')->group(function () {
            Route::get('/cms/menus/create', [CmsMenuController::class, 'create'])->name('admin.cms.menus.create');
            Route::post('/cms/menus', [CmsMenuController::class, 'store'])->name('admin.cms.menus.store');
        });
        Route::middleware('permission:cms.menus.edit')->group(function () {
            Route::get('/cms/menus/{menu}/edit', [CmsMenuController::class, 'edit'])->name('admin.cms.menus.edit');
            Route::put('/cms/menus/{menu}', [CmsMenuController::class, 'update'])->name('admin.cms.menus.update');
        });
        Route::middleware('permission:cms.menus.delete')->group(function () {
            Route::delete('/cms/menus/{menu}', [CmsMenuController::class, 'destroy'])->name('admin.cms.menus.destroy');
        });

        Route::middleware('permission:logs.view')->group(function () {
            Route::get('/logs/activity', [LogsController::class, 'activity'])->name('admin.logs.activity');
            Route::get('/logs/audit', [LogsController::class, 'audit'])->name('admin.logs.audit');
        });

        Route::middleware('permission:roles.view')->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
        });
        Route::middleware('permission:roles.create')->group(function () {
            Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
        });
        Route::middleware('permission:roles.edit')->group(function () {
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
        });
        Route::middleware('permission:roles.delete')->group(function () {
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
        });

        Route::middleware('permission:permissions.view')->group(function () {
            Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
        });
        Route::middleware('permission:permissions.assign')->group(function () {
            Route::get('/permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
            Route::post('/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
            Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('admin.permissions.edit');
            Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');
            Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
        });
    });
});

