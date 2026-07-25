{{-- resources/views/livewire/crisis/crisis-chat.blade.php --}}
<div class="min-h-screen bg-gradient-to-b from-purple-50 to-white">
    <div class="max-w-2xl mx-auto px-4 py-8">
        
        {{-- Header --}}
        <div class="text-center mb-6">
            <div class="text-4xl mb-3">🤝</div>
            <h1 class="text-2xl font-bold text-gray-900">Crisis Support</h1>
            <p class="text-sm text-gray-600">You are anonymous. This conversation is private.</p>
        </div>

        {{-- Privacy Notice --}}
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-start space-x-3">
                <span class="text-xl">🔒</span>
                <div class="text-sm text-green-800">
                    <p class="font-medium mb-1">Your Privacy Is Guaranteed</p>
                    <ul class="space-y-0.5 text-green-700">
                        <li>• No personal data collected</li>
                        <li>• Messages encrypted end-to-end</li>
                        <li>• All data destroyed when you leave</li>
                        <li>• No one can identify you</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Waiting State --}}
        @if($isWaiting)
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <div class="animate-pulse text-4xl mb-4">⏳</div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Connecting you to a counselor...</h2>
                <p class="text-sm text-gray-600">This usually takes less than 1 minute.</p>
                <p class="text-sm text-gray-500 mt-4">If this is an emergency, call <strong>1190</strong> now.</p>
            </div>
        @endif

        {{-- Connected State --}}
        @if($isConnected)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                {{-- Chat Header --}}
                <div class="bg-purple-600 text-white px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                        <span class="font-medium">Counselor Connected</span>
                    </div>
                    <button wire:click="endSession" class="text-white hover:text-purple-200 text-sm">
                        End Chat
                    </button>
                </div>

                {{-- Messages Area --}}
                <div class="h-96 overflow-y-auto p-6 space-y-4" id="chat-messages">
                    @foreach($messages as $message)
                        @if($message['sender_type'] === 'system')
                            <div class="text-center">
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">
                                    {{ $message['content'] }}
                                </span>
                            </div>
                        @elseif($message['sender_type'] === 'user')
                            <div class="flex justify-end">
                                <div class="max-w-xs lg:max-w-md">
                                    <div class="bg-purple-600 text-white rounded-2xl rounded-br-md px-4 py-2">
                                        <p class="text-sm">{{ $message['content'] }}</p>
                                    </div>
                                    <p class="text-xs text-gray-400 text-right mt-1">{{ $message['sent_at'] }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="max-w-xs lg:max-w-md">
                                    <div class="bg-gray-100 text-gray-900 rounded-2xl rounded-bl-md px-4 py-2">
                                        <p class="text-sm">{{ $message['content'] }}</p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">{{ $message['sent_at'] }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Input Area --}}
                <div class="border-t px-6 py-4">
                    <div class="flex space-x-2">
                        <input 
                            type="text" 
                            wire:model="messageText" 
                            wire:keydown.enter="sendMessage"
                            placeholder="Type your message..."
                            class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 px-4 py-3"
                        >
                        <button 
                            wire:click="sendMessage"
                            class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 font-medium"
                        >
                            Send
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Emergency Note --}}
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-6 rounded-r-lg">
            <p class="text-sm text-red-700">
                <strong>If you are in immediate danger</strong>, call 
                <span class="font-bold">1190</span> now.
            </p>
        </div>
    </div>
</div>