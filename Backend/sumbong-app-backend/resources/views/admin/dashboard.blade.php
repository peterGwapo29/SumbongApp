<x-admin-layout>
    <x-slot name="header">
        Admin Dashboard
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Requests -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Total Requests</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_requests'] }}</p>
                </div>
                <div class="flex-shrink-0 ml-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Pending</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_requests'] }}</p>
                </div>
                <div class="flex-shrink-0 ml-4">
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">In Progress</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['in_progress_requests'] }}</p>
                </div>
                <div class="flex-shrink-0 ml-4">
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Resolved</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['resolved_requests'] }}</p>
                </div>
                <div class="flex-shrink-0 ml-4">
                    <div class="p-3 bg-green-50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Total Users</p>
            <p class="text-3xl font-bold text-gray-900 mb-2">{{ $stats['total_users'] }}</p>
            <p class="text-sm text-gray-600">{{ $stats['verified_users'] }} verified</p>
        </div>

        <!-- Active Service Types -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Service Types</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_service_types'] }}</p>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-sm transition-shadow">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-4">Quick Actions</p>
            <div class="space-y-2">
                <a href="{{ route('admin.requests.index') }}" class="flex items-center text-sm text-gray-700 hover:text-gray-900 transition-colors group">
                    <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    View All Requests
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center text-sm text-gray-700 hover:text-gray-900 transition-colors group">
                    <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Manage Users
                </a>
                <a href="{{ route('admin.service-types.index') }}" class="flex items-center text-sm text-gray-700 hover:text-gray-900 transition-colors group">
                    <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Service Types
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Recent Requests</h3>
            <a href="{{ route('admin.requests.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">View all →</a>
        </div>
        <div class="p-6">
            @if($stats['recent_requests']->count() > 0)
                <div class="overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($stats['recent_requests'] as $request)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">#{{ $request->id }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('admin.requests.show', $request->id) }}" class="text-sm font-medium text-gray-900 hover:text-gray-600 transition-colors">
                                            {{ Str::limit($request->title, 40) }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $request->user->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $request->serviceType->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium
                                            @if($request->status === 'created') bg-amber-100 text-amber-800
                                            @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($request->status === 'resolved') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium
                                            @if($request->priority === 'urgent') bg-red-100 text-red-800
                                            @elseif($request->priority === 'high') bg-orange-100 text-orange-800
                                            @elseif($request->priority === 'medium') bg-amber-100 text-amber-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($request->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $request->created_at->format('M d, Y') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900">No requests yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Get started by creating a new request.</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
