<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(HttpRequest $request, $requestId)
    {
        $requestModel = Request::findOrFail($requestId);
        $user = $request->user();

        // Only allow users to add attachments to their own requests, or admins/staff
        if ($requestModel->user_id !== $user->id && !$user->isAdmin() && !$user->isStaff()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('attachments', $fileName, 'public');

        // Determine file type
        $mimeType = $file->getMimeType();
        $fileType = 'document';
        if (str_starts_with($mimeType, 'image/')) {
            $fileType = 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $fileType = 'video';
        }

        $attachment = Attachment::create([
            'request_id' => $requestModel->id,
            'file_url' => Storage::url($filePath),
            'file_type' => $fileType,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $user->id,
        ]);

        return new AttachmentResource($attachment);
    }

    public function destroy($id)
    {
        $attachment = Attachment::findOrFail($id);
        $user = request()->user();

        // Only allow users to delete attachments from their own requests, or admins/staff
        if ($attachment->request->user_id !== $user->id && !$user->isAdmin() && !$user->isStaff()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete file from storage
        $filePath = str_replace('/storage/', '', $attachment->file_url);
        Storage::disk('public')->delete($filePath);

        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully']);
    }
}

