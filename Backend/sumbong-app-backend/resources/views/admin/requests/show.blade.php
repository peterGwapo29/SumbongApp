<x-admin-layout>
    <x-slot name="header">
        Request Details #{{ $requestModel->id }}
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('admin.requests.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Requests</a>
        <div class="flex space-x-2">
            <a href="{{ route('admin.requests.edit', $requestModel->id) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Edit Request
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <!-- Request Overview -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $requestModel->title }}</h2>
                        <p class="text-sm text-gray-500">#{{ $requestModel->id }} - {{ $requestModel->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        @if($requestModel->status === 'created') bg-amber-100 text-amber-700
                        @elseif($requestModel->status === 'assigned') bg-blue-100 text-blue-700
                        @elseif($requestModel->status === 'in_progress') bg-indigo-100 text-indigo-700
                        @elseif($requestModel->status === 'resolved') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $requestModel->status)) }}
                    </span>
                </div>
                <p class="text-gray-700 mb-4">{{ $requestModel->description }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500">Service Type:</p>
                        <p class="font-medium text-gray-900">{{ $requestModel->serviceType->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Priority:</p>
                        <p class="font-medium text-gray-900">
                            <span class="px-2 py-1 text-xs font-medium rounded-md
                                @if($requestModel->priority === 'urgent') bg-red-100 text-red-700
                                @elseif($requestModel->priority === 'high') bg-orange-100 text-orange-700
                                @elseif($requestModel->priority === 'medium') bg-amber-100 text-amber-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($requestModel->priority) }}
                            </span>
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Location:</p>
                        <p class="font-medium text-gray-900">{{ $requestModel->address }}</p>
                        @if($requestModel->barangay || $requestModel->city)
                            <p class="text-sm text-gray-700">{{ $requestModel->barangay ?? '' }}{{ $requestModel->barangay && $requestModel->city ? ', ' : '' }}{{ $requestModel->city ?? '' }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-gray-500">Requested by:</p>
                        <p class="font-medium text-gray-900">{{ $requestModel->user->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700">{{ $requestModel->user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Last Updated:</p>
                        <p class="font-medium text-gray-900">{{ $requestModel->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            @if($requestModel->attachments->count() > 0)
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Attachments</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($requestModel->attachments as $attachment)
                            <a href="{{ asset($attachment->file_url) }}" target="_blank" class="block bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.415a3 3 0 104.243 4.243l6.586-6.586"></path></svg>
                                    <span class="text-sm font-medium text-gray-800 truncate">{{ $attachment->file_name }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ strtoupper($attachment->file_type) }} - {{ round($attachment->file_size / 1024, 2) }} KB</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Status History -->
            @if($requestModel->statusHistory->count() > 0)
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Status History</h3>
                    <div class="relative pl-4">
                        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        @foreach($requestModel->statusHistory->sortBy('created_at') as $history)
                            <div class="relative mb-4 last:mb-0">
                                <div class="absolute -left-2.5 top-0 w-5 h-5 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center">
                                    <div class="w-2.5 h-2.5 rounded-full
                                        @if($history->status === 'created') bg-amber-500
                                        @elseif($history->status === 'assigned') bg-blue-500
                                        @elseif($history->status === 'in_progress') bg-indigo-500
                                        @elseif($history->status === 'resolved') bg-green-500
                                        @else bg-gray-500
                                        @endif"></div>
                                </div>
                                <div class="ml-4">
                                    <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                    <p class="text-xs text-gray-500">{{ $history->created_at->format('M d, Y H:i') }} by {{ $history->changedBy->name ?? 'System' }}</p>
                                    @if($history->notes)
                                        <p class="text-sm text-gray-700 mt-1">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Feedback -->
            @if($requestModel->feedback->count() > 0)
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Feedback</h3>
                    <div class="space-y-4">
                        @foreach($requestModel->feedback as $feedback)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">{{ strtoupper(substr($feedback->user->name ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">{{ $feedback->user->name ?? 'Anonymous' }}</p>
                                        <p class="text-xs text-gray-500">{{ $feedback->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700">{{ $feedback->comment }}</p>
                                @if($feedback->rating)
                                    <div class="flex items-center mt-2">
                                        @for ($i = 0; $i < $feedback->rating; $i++)
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"></path></svg>
                                        @endfor
                                        <span class="ml-1 text-sm text-gray-600">({{ $feedback->rating }}/5)</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1 space-y-4">
            <!-- Quick Status Update -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Quick Status Update</h3>
                <form action="{{ route('admin.requests.status', $requestModel->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            <option value="created" {{ $requestModel->status === 'created' ? 'selected' : '' }}>Created</option>
                            <option value="assigned" {{ $requestModel->status === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ $requestModel->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $requestModel->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $requestModel->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="2"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                  placeholder="Add notes about this status change..."></textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Assignment -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Assignment</h3>

                @if($requestModel->assignments->count() > 0)
                    <div class="mb-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Current Assignments</p>
                        @foreach($requestModel->assignments as $assignment)
                            <div class="flex items-center bg-gray-50 rounded-md p-2 mb-2">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-indigo-700">{{ strtoupper(substr($assignment->user->name ?? 'U', 0, 1)) }}</span>
                                </div>
                                <div class="ml-2 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $assignment->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">Assigned: {{ $assignment->assigned_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('admin.requests.assign', $requestModel->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Assign to Staff</label>
                        <select name="user_id" id="user_id" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            <option value="">Select Staff</option>
                            @foreach($staff as $sUser)
                                <option value="{{ $sUser->id }}" {{ $requestModel->assignments->contains('user_id', $sUser->id) ? 'selected' : '' }}>{{ $sUser->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Assign Request
                    </button>
                </form>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.requests.edit', $requestModel->id) }}" 
                       class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Edit Request
                    </a>
                    <form action="{{ route('admin.requests.destroy', $requestModel->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this request? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
