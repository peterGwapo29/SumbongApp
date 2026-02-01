<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>Requests</span>
            <a href="{{ route('admin.requests.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Request
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg border border-gray-200 mb-4">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.requests.index') }}" class="flex gap-3 flex-wrap items-end">
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">All</option>
                                <option value="created" {{ request('status') == 'created' ? 'selected' : '' }}>Created</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Service Type</label>
                            <select name="service_type_id" class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">All</option>
                                @foreach($serviceTypes as $serviceType)
                                    <option value="{{ $serviceType->id }}" {{ request('service_type_id') == $serviceType->id ? 'selected' : '' }}>
                                        {{ $serviceType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">All</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                                Filter
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('admin.requests.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6">
                    @if($requests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($requests as $request)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">#{{ $request->id }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($request->title, 40) }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $request->user->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $request->serviceType->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-md
                                                    @if($request->status === 'created') bg-amber-100 text-amber-700
                                                    @elseif($request->status === 'in_progress') bg-blue-100 text-blue-700
                                                    @elseif($request->status === 'resolved') bg-green-100 text-green-700
                                                    @else bg-gray-100 text-gray-700
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 py-1 text-xs font-medium rounded-md
                                                    @if($request->priority === 'urgent') bg-red-100 text-red-700
                                                    @elseif($request->priority === 'high') bg-orange-100 text-orange-700
                                                    @elseif($request->priority === 'medium') bg-amber-100 text-amber-700
                                                    @else bg-gray-100 text-gray-700
                                                    @endif">
                                                    {{ ucfirst($request->priority) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('admin.requests.show', $request->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">View</a>
                                                    <a href="{{ route('admin.requests.edit', $request->id) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-sm text-gray-500">No requests found.</p>
                        </div>
                    @endif
                </div>
            </div>
</x-admin-layout>

