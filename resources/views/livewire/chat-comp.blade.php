<div>
    <h1 class="text-xl mb-3">Live Chat</h1>
    <hr>

    <!-- Chat Messages Container -->
    <div class="flow-root mt-5 border-2 p-8 max-h-[650px] overflow-y-auto">
        <ul role="list" class="-mb-8">
            @forelse($messages as $message)
                <li wire:key="{{ $message['id'] }}">
                    <div class="relative pb-8">
                        <div class="relative flex space-x-3">
                            <!-- Icon -->
                            <div>
                                <span class="flex size-8 items-center justify-center rounded-full bg-blue-500 ring-8 ring-white">
                                    <svg class="size-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                            <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                                        </svg>
                                    </svg>
                                </span>
                            </div>
                            <!-- Content -->
                            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        "{{ $message['body'] }}"
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        - {{ $message['user'] }}
                                    </p>
                                </div>
                                <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                    <time datetime="{{ $message['created'] }}">
                                        {{ \Carbon\Carbon::parse($message['created'])->format('M d, Y h:i A') }}
                                    </time>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="text-center text-gray-500">
                    No messages yet. Start the conversation!
                </li>
            @endforelse
        </ul>
    </div>

    <br>

    <!-- Message Input Form -->
    <div class="flex items-start space-x-4">
        <div class="min-w-0 flex-1">
            <form wire:submit.prevent="sendMessage">
                <div class="border-gray-200 pb-px focus-within:border-b-2 focus-within:border-indigo-600 focus-within:pb-0">
                    <label for="comment" class="sr-only">Add your comment</label>
                    <textarea
                        rows="3"
                        name="comment"
                        id="comment"
                        class="block w-full resize-none text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6"
                        placeholder="Add your comment..."
                        wire:model.debounce.150ms="chatMessage"
                    ></textarea>
                </div>
                @error('chatMessage')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
                <div class="flex justify-end pt-2">
                    <div class="shrink-0">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Post
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Frontend Listener for Real-Time Updates -->
<script>
    document.addEventListener('livewire:load', function () {
        window.Echo.private('cbros-chat-111') // Updated channel name
            .listen('.new-message', (e) => {
                Livewire.emit('redisNewMessage', e.message);
            });
    });
</script>
