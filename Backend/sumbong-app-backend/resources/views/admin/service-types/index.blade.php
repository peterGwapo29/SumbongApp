<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>Service Types</span>
            <a href="{{ route('admin.service-types.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                Create New
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200">
        <div class="p-6">
            @if($serviceTypes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($serviceTypes as $serviceType)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">#{{ $serviceType->id }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $serviceType->icon ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $serviceType->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $serviceType->department }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ Str::limit($serviceType->description, 50) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($serviceType->is_active)
                                            <span class="px-2 py-1 text-xs font-medium rounded-md bg-green-100 text-green-700">Active</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-700">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <a href="{{ route('admin.service-types.edit', $serviceType->id) }}" class="text-gray-700 hover:text-gray-900 font-medium mr-3">Edit</a>
                                        <form method="POST" action="{{ route('admin.service-types.destroy', $serviceType->id) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $serviceTypes->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-sm text-gray-500">No service types found.</p>
                </div>
            @endif
                </div>
            </div>
</x-admin-layout>

