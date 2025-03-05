<div id="sms-inbox" class="fixed bottom-4 right-4 z-50">
    <button id="toggle-inbox" class="bg-blue-500 text-white p-4 rounded-full shadow-lg focus:outline-none transition-transform hover:scale-110 animate-bounce">
        📬
    </button>

    <div id="inbox-panel" class="hidden w-96 bg-white border border-gray-300 rounded-lg shadow-2xl p-4 max-h-[500px] overflow-y-auto transition-all duration-500 ease-in-out transform translate-y-4 opacity-0">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">📬 SMS Inbox</h2>
            <button id="close-inbox" class="text-gray-500 hover:text-gray-800 transition">✖️</button>
        </div>

        @forelse($messages as $message)
            <div class="border border-gray-200 p-4 mb-4 rounded-lg bg-gray-50 hover:shadow-md transition">
                <p><strong>From:</strong> <span class="text-blue-600 font-medium">{{ $message->from ?? 'Unknown' }}</span></p>
                <p><strong>Time:</strong> <span class="text-gray-700">{{ optional($message->dateSent)->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s') }}</span></p>
                <p><strong>Message:</strong> <span class="text-gray-800">{{ $message->body ?? 'No content' }}</span></p>

                <form class="reply-form mt-3" action="{{ route('send.reply') }}" method="POST">
                    @csrf
                    <textarea name="message" placeholder="Type your reply..." required class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 transition"></textarea>
                    <input type="hidden" name="to" value="{{ $message->from }}">
                    <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">Reply</button>
                </form>
            </div>
        @empty
            <p class="text-gray-600">No messages found.</p>
        @endforelse

        <!-- Custom Message Form -->
        <div class="mt-6">
            <h3 class="text-lg font-bold mb-4">Send Custom Message</h3>
            <form action="{{ route('messages.send') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md">
                @csrf
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea name="message" id="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required></textarea>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Send Message</button>
            </form>
        </div>
    </div>
</div>
