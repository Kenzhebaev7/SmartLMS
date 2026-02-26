<div class="p-4 rounded-xl border border-gray-200 {{ $comment->parent_id ? 'bg-gray-50 ml-8' : 'bg-white' }}">
    <p class="font-medium text-slate-800">{{ $comment->user->name }}</p>
    <p class="text-sm text-gray-500">{{ $comment->created_at->format('d.m.Y H:i') }}</p>
    <p class="mt-2 text-slate-700">{{ $comment->body }}</p>
    @auth
        <form action="{{ route('forum.comments.store', $comment->thread) }}" method="POST" class="mt-3 hidden reply-form" data-parent-id="{{ $comment->id }}">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <textarea name="body" rows="2" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="{{ __('forum.reply_placeholder') }}"></textarea>
            <button type="submit" class="mt-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-light transition-colors">{{ __('forum.send') }}</button>
        </form>
        <button type="button" class="mt-2 text-sm text-primary hover:underline reply-btn" data-target="{{ $comment->id }}">{{ __('forum.reply') }}</button>
        @if(in_array(auth()->user()->role, [\App\Models\User::ROLE_TEACHER, \App\Models\User::ROLE_ADMIN]))
            <form action="{{ route('forum.comments.destroy', $comment) }}" method="POST" class="inline ml-2" onsubmit="return confirm('{{ __('forum.confirm_hide_comment') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-amber-600 hover:text-amber-800 font-medium">{{ __('forum.hide_comment') }}</button>
            </form>
        @endif
    @endauth
    @foreach($comment->replies ?? [] as $reply)
        @include('forum._comment', ['comment' => $reply])
    @endforeach
</div>
