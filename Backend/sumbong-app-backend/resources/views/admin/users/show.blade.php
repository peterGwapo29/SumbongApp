<x-admin-layout>
    <x-slot name="header">
        User Details
    </x-slot>
            <div class="mb-4">
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Users</a>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 mb-4">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">User #{{ $user->id }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="font-medium">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Mobile</p>
                            <p class="font-medium">{{ $user->mobile ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Role</p>
                            <p class="font-medium">{{ $user->role->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">User Type</p>
                            <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Verified</p>
                            <p class="font-medium">
                                @if($user->verified)
                                    <span class="text-green-600">Yes</span>
                                @else
                                    <span class="text-gray-600">No</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Address</p>
                            <p class="font-medium">{{ $user->address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Created</p>
                            <p class="font-medium">{{ $user->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">User Requests ({{ $user->requests->count() }})</h3>
                    @if($user->requests->count() > 0)
                        <div class="space-y-2">
                            @foreach($user->requests->take(10) as $request)
                                <div class="border-b pb-2">
                                    <a href="{{ route('admin.requests.show', $request->id) }}" class="text-blue-600 hover:text-blue-800">
                                        #{{ $request->id }} - {{ $request->title }}
                                    </a>
                                    <span class="text-sm text-gray-500 ml-2">{{ $request->status }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No requests yet.</p>
                    @endif
                </div>
            </div>
</x-admin-layout>

