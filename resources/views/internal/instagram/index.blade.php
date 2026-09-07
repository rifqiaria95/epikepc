@extends('layouts.main')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('internal.partials.stat-cards', ['stats' => $stats])

        <div class="row g-6 mb-6">
            <div class="col-lg-8">
                <div class="card mb-6">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="mb-1">Connection status</h5>
                            <p class="mb-0 text-muted">Instagram tokens are stored in environment configuration and are never shown here.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @can('sync_instagram')
                                <button type="button" class="btn btn-outline-primary btn-sync" data-scope="feed">Sync Feed Now</button>
                                <button type="button" class="btn btn-outline-primary btn-sync" data-scope="stories">Sync Stories Now</button>
                                <button type="button" class="btn btn-primary btn-sync" data-scope="all">Refresh All</button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            @if ($settings->profile_picture_url)
                                <img src="{{ $settings->profile_picture_url }}" alt="" width="56" height="56" class="rounded-circle" style="object-fit:cover;">
                            @endif
                            <div>
                                <strong>{{ $settings->username ? '@'.$settings->username : 'Not connected' }}</strong>
                                <div class="text-muted small">{{ $settings->profile_url ?: 'Profile URL will appear after a successful sync.' }}</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">Environment</div><strong>{{ $configEnabled ? 'Enabled' : 'Disabled' }}</strong></div></div>
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">Section</div><strong>{{ $settings->enabled ? 'Visible' : 'Hidden' }}</strong></div></div>
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">Credentials</div><strong>{{ $configured ? 'Present' : 'Missing' }}</strong></div></div>
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">API status</div><strong>{{ ucfirst($settings->api_status ?: 'unknown') }}</strong></div></div>
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">Token status</div><strong>{{ ucfirst($settings->token_status ?: 'unknown') }}</strong></div></div>
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">Last feed sync</div><strong>{{ optional($settings->last_successful_feed_sync_at)?->format('d M Y H:i') ?: '—' }}</strong></div></div>
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">Last story sync</div><strong>{{ optional($settings->last_successful_story_sync_at)?->format('d M Y H:i') ?: '—' }}</strong></div></div>
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">Last failed sync</div><strong>{{ optional($settings->last_failed_sync_at)?->format('d M Y H:i') ?: '—' }}</strong></div></div>
                            <div class="col-md-4"><div class="border rounded p-3"><div class="text-muted small">Last error</div><strong>{{ $settings->last_error_message ?: 'None' }}</strong></div></div>
                        </div>
                    </div>
                </div>

                @can('manage_instagram')
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">Section content</h5>
                        </div>
                        <div class="card-body">
                            <form id="formInstagramSettings">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="enabled">Show section</label>
                                        <select class="form-select" id="enabled" name="enabled">
                                            <option value="1" @selected($settings->enabled)>Enabled</option>
                                            <option value="0" @selected(!$settings->enabled)>Disabled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="feed_limit">Feed items</label>
                                        <input type="number" min="1" max="{{ (int) config('instagram.feed_limit', 6) }}" class="form-control" id="feed_limit" name="feed_limit" value="{{ $settings->feed_limit }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="profile_url">Profile URL override</label>
                                        <input type="url" class="form-control" id="profile_url" name="profile_url" value="{{ $settings->profile_url }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="eyebrow">Eyebrow</label>
                                        <input type="text" class="form-control" id="eyebrow" name="eyebrow" value="{{ $settings->eyebrow }}">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label" for="heading">Heading</label>
                                        <input type="text" class="form-control" id="heading" name="heading" value="{{ $settings->heading }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="subtitle">Subtitle</label>
                                        <input type="text" class="form-control" id="subtitle" name="subtitle" value="{{ $settings->subtitle }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="cta_label">Follow button label</label>
                                        <input type="text" class="form-control" id="cta_label" name="cta_label" value="{{ $settings->cta_label }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="view_more_label">View more label</label>
                                        <input type="text" class="form-control" id="view_more_label" name="view_more_label" value="{{ $settings->view_more_label }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-4" id="btn-save-settings">Save settings</button>
                            </form>
                        </div>
                    </div>
                @endcan

                @canany(['publish_instagram', 'manage_instagram'])
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-1">Create / schedule feed post</h5>
                            <p class="mb-0 text-muted small">
                                Publish a photo (JPEG) or video/Reel (MP4/MOV) to Instagram via Meta Content Publishing.
                                Scheduling is stored locally; the container is created only at publish time (containers expire in 24 hours).
                                Media must be reachable by Meta over public HTTPS (<code>APP_URL</code>).
                            </p>
                        </div>
                        <div class="card-body">
                            <form id="formInstagramPost" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label" for="post_media_type">Media type</label>
                                        <select class="form-select" id="post_media_type" name="media_type" required>
                                            <option value="IMAGE">Photo (JPEG)</option>
                                            <option value="REELS">Video / Reels</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label" for="post_media">Media file</label>
                                        <input type="file" class="form-control" id="post_media" name="media" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="post_scheduled_at">Schedule at</label>
                                        <input type="datetime-local" class="form-control" id="post_scheduled_at" name="scheduled_at">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="post_caption">Caption</label>
                                        <textarea class="form-control" id="post_caption" name="caption" rows="3" maxlength="2200" placeholder="Caption (max 2200 characters)"></textarea>
                                    </div>
                                    <div class="col-12 d-flex flex-wrap gap-2">
                                        <button type="submit" class="btn btn-primary" id="btn-schedule-post" data-publish-now="0">Schedule post</button>
                                        <button type="submit" class="btn btn-outline-primary" id="btn-publish-now" data-publish-now="1">Publish now</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>When</th>
                                            <th>Type</th>
                                            <th>Caption</th>
                                            <th>Status</th>
                                            <th>Error</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($posts as $post)
                                            <tr>
                                                <td class="small">
                                                    {{ optional($post->scheduled_at)?->timezone(config('app.timezone'))->format('d M Y H:i') ?: '—' }}
                                                    @if ($post->published_at)
                                                        <div class="text-muted">Published {{ $post->published_at->timezone(config('app.timezone'))->format('d M Y H:i') }}</div>
                                                    @endif
                                                </td>
                                                <td>{{ $post->media_type->value }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit(strip_tags((string) $post->caption), 60) ?: '—' }}</td>
                                                <td><span class="badge bg-label-secondary">{{ $post->status->label() }}</span></td>
                                                <td class="small text-danger">{{ $post->last_error_message ?: '—' }}</td>
                                                <td class="text-nowrap">
                                                    @if ($post->status->isCancellable())
                                                        <button type="button" class="btn btn-sm btn-outline-secondary ig-post-cancel" data-id="{{ $post->id }}">Cancel</button>
                                                    @endif
                                                    @if (in_array($post->status->value, ['scheduled', 'failed'], true))
                                                        <button type="button" class="btn btn-sm btn-outline-primary ig-post-publish" data-id="{{ $post->id }}">Publish now</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-muted">No scheduled or published CMS posts yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endcanany

                @can('manage_instagram')
                    <div class="card mb-6">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h5 class="mb-1">Story Highlights</h5>
                                <p class="mb-0 text-muted small">
                                    Meta Graph API does not expose Instagram Highlights. Manage cover, title, and highlight URL here so the homepage rings match the account.
                                </p>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="formInstagramHighlight" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label" for="hl_title">Title</label>
                                        <input type="text" class="form-control" id="hl_title" name="title" maxlength="80" placeholder="Projects" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="hl_permalink">Highlight URL</label>
                                        <input type="url" class="form-control" id="hl_permalink" name="permalink" placeholder="https://www.instagram.com/stories/highlights/..." required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="hl_cover_url">Cover URL</label>
                                        <input type="text" class="form-control" id="hl_cover_url" name="cover_url" placeholder="/storage/... or https://...">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="hl_cover">Or upload</label>
                                        <input type="file" class="form-control" id="hl_cover" name="cover" accept="image/*">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary" id="btn-save-highlight">Add highlight</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table mb-0" id="instagramHighlightsTable">
                                    <thead>
                                        <tr>
                                            <th>Cover</th>
                                            <th>Title</th>
                                            <th>Permalink</th>
                                            <th>Visible</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($highlights as $highlight)
                                            <tr data-id="{{ $highlight->id }}">
                                                <td>
                                                    <img src="{{ $highlight->cover_url }}" alt="" width="48" height="48" style="object-fit:cover;border-radius:50%;">
                                                </td>
                                                <td>{{ $highlight->title }}</td>
                                                <td class="small">
                                                    <a href="{{ $highlight->permalink }}" target="_blank" rel="noopener noreferrer">Open</a>
                                                </td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input ig-highlight-visibility" type="checkbox" data-id="{{ $highlight->id }}" @checked($highlight->is_visible)>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ig-highlight-delete" data-id="{{ $highlight->id }}">Delete</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-muted">No highlights yet. Add the account's saved Highlights with title, cover, and Instagram highlight URL.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endcan

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Feed snapshot</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="instagramFeedTable">
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th>Type</th>
                                    <th>Caption</th>
                                    <th>Published</th>
                                    <th>Visible</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($media as $item)
                                    <tr data-id="{{ $item->id }}">
                                        <td>
                                            @if ($item->previewUrl())
                                                <img src="{{ $item->previewUrl() }}" alt="" width="56" height="56" style="object-fit:cover;border-radius:4px;">
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $item->media_type->value }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit(strip_tags((string) $item->caption), 80) }}</td>
                                        <td>{{ optional($item->published_at)?->format('d M Y H:i') ?: '—' }}</td>
                                        <td>
                                            @can('manage_instagram')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input ig-visibility" type="checkbox" data-id="{{ $item->id }}" @checked($item->is_visible)>
                                                </div>
                                            @else
                                                {{ $item->is_visible ? 'Yes' : 'No' }}
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted">No feed snapshot yet. Run a sync after connecting Instagram.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Homepage preview</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Public visitors only see visible feed items and unexpired stories. Access tokens are never sent to the browser.</p>
                        <div class="border rounded p-3 bg-lighter">
                            <div class="fw-semibold mb-2">{{ $widget['heading'] }}</div>
                            <div class="small mb-3">Rings: {{ count($widget['rings'] ?? []) }} · Live stories: {{ count($widget['stories']) }} · Feed: {{ count($widget['feed']) }}</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($widget['feed'] as $preview)
                                    <img src="{{ $preview['preview_url'] ?: $widget['fallback_image'] }}" alt="" width="72" height="72" style="object-fit:cover;border-radius:4px;">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
        window.instagramRoutes = {
            settings: @json(route('instagram.settings')),
            sync: @json(route('instagram.sync')),
            toggle: @json(url('/internal/instagram/media')),
            highlights: @json(route('instagram.highlights.store')),
            highlightBase: @json(url('/internal/instagram/highlights')),
            posts: @json(route('instagram.posts.store')),
            postBase: @json(url('/internal/instagram/posts')),
        };
    </script>
    <script src="{{ asset('assets/ajax/instagram.js') }}"></script>
@endsection
