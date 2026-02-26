<x-app-layout>
    <x-slot name="header">Форум — SmartLMS</x-slot>

    @if(session('status'))
        <div class="mb-6 rounded-xl bg-primary-50 border border-primary-200 px-4 py-3 text-primary-light">{{ session('status') }}</div>
    @endif

    <div class="mb-6 p-6 rounded-xl border border-gray-200 bg-white">
        <h2 class="text-xl font-bold text-slate-800">{{ $thread->title }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $thread->user->name }} · {{ $thread->created_at->format('d.m.Y H:i') }}</p>
        <div class="mt-4 text-slate-700 whitespace-pre-wrap">{{ $thread->body }}</div>
        @auth
            @if(in_array(auth()->user()->role, [\App\Models\User::ROLE_TEACHER, \App\Models\User::ROLE_ADMIN]))
                <form action="{{ route('forum.threads.destroy', $thread) }}" method="POST" class="mt-4 inline" onsubmit="return confirm('{{ __('forum.confirm_hide_topic') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-amber-600 hover:text-amber-800 font-medium">{{ __('forum.hide_topic') }}</button>
                </form>
            @endif
        @endauth
    </div>

    <h3 class="text-lg font-bold text-slate-800 mb-4">{{ __('forum.comments') }}</h3>
    <div class="space-y-4 mb-8">
        @foreach($thread->comments ?? [] as $comment)
            @include('forum._comment', ['comment' => $comment])
        @endforeach
    </div>

    <form action="{{ route('forum.comments.store', $thread) }}" method="POST" class="max-w-xl">
        @csrf
        <input type="hidden" name="parent_id" value="">
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('forum.your_comment') }}</label>
        <textarea name="body" rows="3" required class="w-full rounded-lg border border-gray-300 px-4 py-2"></textarea>
        @error('body')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        <button type="submit" class="mt-3 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-light transition-colors">Отправить</button>
    </form>

    <div class="mt-6">
        <a href="{{ route('forum.index') }}" class="text-primary font-semibold hover:underline">← {{ __('forum.back_list') }}</a>
    </div>

    <script>
        document.querySelectorAll('.reply-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var form = this.closest('.rounded-xl').querySelector('.reply-form');
                if (form) {
                    form.classList.toggle('hidden');
                    if (!form.classList.contains('hidden')) form.querySelector('textarea').focus();
                }
            });
        });
    </script>
</x-app-layout>
