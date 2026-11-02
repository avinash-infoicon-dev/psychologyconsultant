<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsTeamMember;
use App\Services\Cms\CmsUploadService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeamMemberController extends Controller
{
    public function __construct(protected CmsUploadService $uploads) {}

    public function index(Request $request): View
    {
        $members = CmsTeamMember::query()
            ->with('translations')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->whereHas('translations', function ($t) use ($q) {
                    $t->where('name', 'like', $q)->orWhere('role', 'like', $q);
                });
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('backend.cms.team.index', compact('members'));
    }

    public function create(): View
    {
        return view('backend.cms.team.create', ['locales' => Locales::codes()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $member = CmsTeamMember::query()->create($data['attrs']);
            $this->sync($member, $request, $data['translations']);
        });

        return redirect()->route('admin.cms.team.index')
            ->with('success', __('backend.cms.team.created'));
    }

    public function edit(CmsTeamMember $teamMember): View
    {
        $teamMember->load('translations');

        return view('backend.cms.team.edit', [
            'member' => $teamMember,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request, CmsTeamMember $teamMember): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($teamMember, $request, $data) {
            $teamMember->update($data['attrs']);
            $this->sync($teamMember, $request, $data['translations']);
        });

        return redirect()->route('admin.cms.team.index')
            ->with('success', __('backend.cms.team.updated'));
    }

    public function destroy(CmsTeamMember $teamMember): RedirectResponse
    {
        $this->uploads->delete($teamMember->photo_path, 'cms/team/');
        $teamMember->delete();

        return redirect()->route('admin.cms.team.index')
            ->with('success', __('backend.cms.team.deleted'));
    }

    protected function validated(Request $request): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.name"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.role"] = ['nullable', 'string', 'max:255'];
        }

        $data = $request->validate(array_merge([
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'profile_url' => ['nullable', 'string', 'max:500'],
            'facebook_url' => ['nullable', 'string', 'max:500'],
            'twitter_url' => ['nullable', 'string', 'max:500'],
            'linkedin_url' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        return [
            'attrs' => [
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $data['sort_order'] ?? 0,
                'profile_url' => $data['profile_url'] ?? null,
                'facebook_url' => $data['facebook_url'] ?? null,
                'twitter_url' => $data['twitter_url'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
            ],
            'translations' => $data['translations'],
        ];
    }

    protected function sync(CmsTeamMember $member, Request $request, array $translations): void
    {
        $path = $member->photo_path;
        if ($request->boolean('remove_photo')) {
            $this->uploads->delete($path, 'cms/team/');
            $path = null;
        }
        if ($file = $request->file('photo')) {
            $this->uploads->delete($path, 'cms/team/');
            $path = $this->uploads->store('cms/team/'.$member->id, $file)['path'];
        }
        $member->update(['photo_path' => $path]);

        foreach (Locales::codes() as $locale) {
            $member->translations()->updateOrCreate(
                ['locale' => $locale],
                $translations[$locale] ?? []
            );
        }
    }
}
