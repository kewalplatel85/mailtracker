<div id="sms-inbox" class="fixed bottom-4 right-4 z-50">
    <!-- Floating Toggle Button -->
    <button id="toggle-inbox" class="bg-blue-500 text-white p-4 rounded-full shadow-lg focus:outline-none">
        📬
    </button>

    <!-- SMS Inbox Panel -->
    <div id="inbox-panel" class="hidden w-96 bg-white border border-gray-300 rounded-lg shadow-lg p-4 max-h-[500px] overflow-y-auto">
        <h2 class="text-lg font-bold mb-4">📬 SMS Inbox</h2>

        <!-- Messages Loop -->
        @foreach($messages as $message)
            <div class="border border-gray-300 p-4 mb-4 rounded-lg">
                <p><strong>From:</strong> {{ $message->from }}</p>
                <p><strong>Time:</strong> {{ $message->dateSent->format('Y-m-d H:i:s') }}</p>
                <p><strong>Message:</strong> {{ $message->body }}</p>

                <!-- Reply Form -->
                <form class="reply-form mt-3" action="{{ route('send.reply') }}" method="POST">
                    @csrf
                    <textarea name="message" placeholder="Type your reply..." required class="w-full p-2 border rounded-md"></textarea>
                    <input type="hidden" name="to" value="{{ $message->from }}">
                    <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Reply
                    </button>
                </form>
            </div>
        @endforeach

        <!-- Success Message -->
        @if(session('success'))
            <p class="text-green-600">{{ session('success') }}</p>
        @endif
    </div>
</div>
