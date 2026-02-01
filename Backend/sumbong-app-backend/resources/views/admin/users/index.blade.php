<x-admin-layout>
    <x-slot name="header">
        Manage Users
    </x-slot>
            <!-- Filters -->
            <div class="bg-white rounded-lg border border-gray-200 mb-4">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-3 flex-wrap items-end">
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email" class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">User Type</label>
                            <select name="user_type" class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">All</option>
                                <option value="resident" {{ request('user_type') == 'resident' ? 'selected' : '' }}>Resident</option>
                                <option value="non_resident" {{ request('user_type') == 'non_resident' ? 'selected' : '' }}>Non-Resident</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Verified</label>
                            <select name="verified" class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">All</option>
                                <option value="1" {{ request('verified') == '1' ? 'selected' : '' }}>Verified</option>
                                <option value="0" {{ request('verified') == '0' ? 'selected' : '' }}>Not Verified</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6">
                    @if($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verified</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">#{{ $user->id }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $user->role->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($user->verified)
                                                    <span class="px-2 py-1 text-xs font-medium rounded-md bg-green-100 text-green-700">Verified</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-700">Not Verified</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-gray-700 hover:text-gray-900 font-medium">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-sm text-gray-500">No users found.</p>
                        </div>
                    @endif
                </div>
            </div>
</x-admin-layout>

