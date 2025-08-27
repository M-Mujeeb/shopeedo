<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\RefundRequestCollection;
use App\Models\OrderDetail;
use App\Models\RefundRequest;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Storage;

class RefundRequestController extends Controller
{

    public function get_list()
    {
        $refunds = RefundRequest::where('user_id', auth()->user()->id)->latest()->paginate(10);

        return new RefundRequestCollection($refunds);
    }

    public function send(Request $request)
    {
        // 1) Validate the exact payload you showed
        $request->validate([
            'id' => ['required', 'integer', 'exists:order_details,id'],
            'reason' => ['required', 'string', 'min:3'],
            'attachments' => ['required', 'array', 'min:1'],
            'attachments.*.image' => ['required', 'string'],
            'attachments.*.filename' => ['nullable', 'string'],
        ]);

        $orderDetail = OrderDetail::with('order')->findOrFail($request->id);

        if ($orderDetail->order && $orderDetail->order->user_id !== auth()->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $uploadIds = [];
        foreach ($request->attachments as $i => $att) {
            $image = $att['image'] ?? '';
            $filename = $att['filename'] ?? ('attachment_' . $i . '.jpg');

            $id = $this->storeBase64DataUriImage($image, $filename, auth()->user()->id);
            if ($id) {
                $uploadIds[] = $id;
            }
        }

        if (empty($uploadIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid attachments provided'
            ], 422);
        }

        $refund = new RefundRequest();
        $refund->user_id = auth()->user()->id;
        $refund->order_id = $orderDetail->order_id;
        $refund->order_detail_id = $orderDetail->id;
        $refund->seller_id = $orderDetail->seller_id;
        $refund->seller_approval = 1;
        $refund->reason = $request->reason;
        $refund->attachments = implode(',', $uploadIds);
        $refund->admin_approval = 0;
        $refund->admin_seen = 0;
        $refund->refund_amount = $orderDetail->price + $orderDetail->tax;
        $refund->refund_status = 0;
        $refund->save();

        return response()->json([
            'success' => true,
            'message' => translate('Request Sent'),
            'attachments' => $refund->attachments,
        ]);
    }

    private function storeBase64DataUriImage(string $dataUri, string $filename, int $userId): ?int
    {
        if (!str_starts_with($dataUri, 'data:image')) {
            return null;
        }

        // Parse mime from header
        if (!preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,/', $dataUri, $m)) {
            return null;
        }
        $mime = $m[1];

        // Strip header
        $parts = explode(',', $dataUri, 2);
        $base64 = $parts[1] ?? '';
        $binary = base64_decode($base64, true);
        if ($binary === false) {
            return null;
        }

        // Map mime -> extension (fallback from filename if present)
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
        ];
        $ext = $mimeToExt[$mime] ?? strtolower(pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg');
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
            $ext = 'jpg';
        }

        // Ensure destination
        $relativeDir = 'uploads/all';
        $absDir = public_path($relativeDir);
        if (!File::exists($absDir)) {
            File::makeDirectory($absDir, 0775, true, true);
        }

        // Generate new name & write file
        $newName = Str::random(16) . date('YmdHis') . '.' . $ext;
        $relativePath = $relativeDir . '/' . $newName;
        $absPath = $absDir . '/' . $newName;

        if (file_put_contents($absPath, $binary) === false) {
            return null;
        }

        $size = File::size($absPath) ?: 0;

        // Optional S3
        if (env('FILESYSTEM_DRIVER') === 's3') {
            Storage::disk('s3')->put($relativePath, file_get_contents($absPath), ['visibility' => 'public']);
            @unlink($absPath);
        }

        // Create Upload row
        $upload = new Upload();
        $upload->file_original_name = pathinfo($filename, PATHINFO_FILENAME) ?: 'attachment';
        $upload->extension = $ext;
        $upload->file_name = $relativePath;
        $upload->user_id = $userId;
        $upload->type = 'image';
        $upload->file_size = $size;
        $upload->save();

        return $upload->id;
    }
}
