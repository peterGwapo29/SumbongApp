<x-admin-layout>
    <x-slot name="header">
        Create Service Type
    </x-slot>
            <div class="mb-4">
                <a href="{{ route('admin.service-types.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Service Types</a>
            </div>

            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.service-types.store') }}">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">{{ old('description') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                                <input type="text" name="department" value="{{ old('department') }}" required class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                @error('department') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
                                <input type="text" name="icon" value="{{ old('icon') }}" maxlength="10" class="w-full text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-600 focus:ring-gray-500">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                                    Create Service Type
                                </button>
                                <a href="{{ route('admin.service-types.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
</x-admin-layout>

