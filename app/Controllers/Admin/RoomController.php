<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Media;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\RoomImage;

class RoomController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('content.manage');
        $search = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? '';
        $categoryId = !empty($_GET['category']) ? (int) $_GET['category'] : null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pager = Room::paginate($page, 15, $search ?: null, $status ?: null, $categoryId);

        $this->view('admin/rooms/index', [
            'title' => 'Rooms',
            'rooms' => $pager['data'],
            'pager' => $pager,
            'search' => $search,
            'statusFilter' => $status,
            'categoryFilter' => $categoryId,
            'categories' => RoomCategory::all(),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('content.manage');
        clear_validation_state();
        $this->view('admin/rooms/create', $this->formData(null));
    }

    public function store(): void
    {
        $this->requirePermission('content.manage');
        $input = $this->inputFromPost();
        if (!$this->validate($input, [
            'name' => 'required|max:190',
            'slug' => 'required|max:190|unique:rooms,slug',
            'status' => 'required|in:draft,published',
            'price_per_night' => 'required',
        ])) {
            $this->back();
        }
        $payload = $this->roomPayload($input, null);
        if ($payload['image_path'] === null) {
            Session::flash('error', 'Please upload an image or select one from the Media library.');
            Session::set('old_input', $_POST);
            $this->back();
        }
        $roomId = Room::create($payload);
        $this->syncGallery($roomId);
        Session::flash('success', 'Room created.');
        $this->redirect(url('/admin/rooms'));
    }

    public function edit(int $id): void
    {
        $this->requirePermission('content.manage');
        $room = Room::find($id);
        if (!$room) {
            Session::flash('error', 'Room not found.');
            $this->redirect(url('/admin/rooms'));
        }
        $this->view('admin/rooms/edit', $this->formData($room));
    }

    public function update(int $id): void
    {
        $this->requirePermission('content.manage');
        $existing = Room::find($id);
        if (!$existing) {
            Session::flash('error', 'Room not found.');
            $this->redirect(url('/admin/rooms'));
        }
        $input = $this->inputFromPost();
        if (!$this->validate($input, [
            'name' => 'required|max:190',
            'slug' => 'required|max:190|unique:rooms,slug',
            'status' => 'required|in:draft,published',
            'price_per_night' => 'required',
        ], $id)) {
            $this->back();
        }
        $payload = $this->roomPayload($input, $existing);
        Room::update($id, $payload);
        $this->syncGallery($id);
        Session::flash('success', 'Room updated.');
        $this->redirect(url('/admin/rooms'));
    }

    public function delete(int $id): void
    {
        $this->requirePermission('content.manage');
        $room = Room::find($id);
        if ($room) {
            delete_upload_file($room['image_path'] ?? null);
            foreach (RoomImage::pathsForRoom($id) as $path) {
                if ($path !== ($room['image_path'] ?? '')) {
                    delete_upload_file($path);
                }
            }
            Room::delete($id);
        }
        Session::flash('success', 'Room deleted.');
        $this->redirect(url('/admin/rooms'));
    }

    public function toggleStatus(int $id): void
    {
        $this->requirePermission('content.manage');
        $room = Room::find($id);
        if (!$room) {
            Session::flash('error', 'Room not found.');
            $this->redirect(url('/admin/rooms'));
        }
        $newStatus = $room['status'] === 'published' ? 'draft' : 'published';
        Room::update($id, array_merge($this->roomPayloadFromRow($room), [
            'status' => $newStatus,
        ]));
        Session::flash('success', 'Room is now ' . $newStatus . '.');
        $this->redirect(url('/admin/rooms'));
    }

    private function formData(?array $room): array
    {
        $gallery = $room ? RoomImage::forRoom((int) $room['id']) : [];
        return [
            'title' => $room ? 'Edit Room' : 'Create Room',
            'room' => $room,
            'categories' => RoomCategory::allActive(),
            'allCategories' => RoomCategory::all(),
            'mediaImages' => Media::images(80),
            'amenityOptions' => default_amenity_options(),
            'selectedAmenities' => $room ? amenities_from_json($room['amenities'] ?? null) : [],
            'gallery' => $gallery,
        ];
    }

    private function inputFromPost(): array
    {
        return [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => slugify($_POST['slug'] ?? $_POST['name'] ?? ''),
            'category_id' => trim($_POST['category_id'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'price_per_night' => trim($_POST['price_per_night'] ?? '0'),
            'max_guests' => trim($_POST['max_guests'] ?? '2'),
            'status' => $_POST['status'] ?? 'draft',
            'sort_order' => trim($_POST['sort_order'] ?? '0'),
            'is_featured' => isset($_POST['is_featured']) ? '1' : '0',
            'media_image_id' => trim($_POST['media_image_id'] ?? ''),
            'amenities' => $_POST['amenities'] ?? [],
            'amenities_custom' => trim($_POST['amenities_custom'] ?? ''),
            'gallery_media_ids' => $_POST['gallery_media_ids'] ?? [],
        ];
    }

    private function roomPayload(array $input, ?array $existing): array
    {
        $imagePath = $this->resolveImagePath($existing['image_path'] ?? null);

        return [
            'category_id' => $input['category_id'] !== '' ? (int) $input['category_id'] : null,
            'name' => $input['name'],
            'slug' => $input['slug'],
            'description' => $input['description'],
            'amenities' => amenities_to_json(
                is_array($input['amenities']) ? $input['amenities'] : [],
                $input['amenities_custom']
            ),
            'price_per_night' => (float) $input['price_per_night'],
            'max_guests' => max(1, (int) $input['max_guests']),
            'image_path' => $imagePath,
            'status' => $input['status'],
            'is_featured' => !empty($input['is_featured']) ? 1 : 0,
            'sort_order' => (int) $input['sort_order'],
        ];
    }

    private function roomPayloadFromRow(array $room): array
    {
        return [
            'category_id' => $room['category_id'],
            'name' => $room['name'],
            'slug' => $room['slug'],
            'description' => $room['description'],
            'amenities' => $room['amenities'],
            'price_per_night' => $room['price_per_night'],
            'max_guests' => $room['max_guests'],
            'image_path' => $room['image_path'],
            'status' => $room['status'],
            'is_featured' => $room['is_featured'],
            'sort_order' => $room['sort_order'],
        ];
    }

    private function resolveImagePath(?string $current): ?string
    {
        if (!empty($_FILES['image']['name'])) {
            $path = upload_file($_FILES['image'], 'rooms');
            if ($path) {
                if ($current) {
                    delete_upload_file($current);
                }
                return $path;
            }
        }

        $mediaId = (int) ($_POST['media_image_id'] ?? 0);
        if ($mediaId > 0) {
            $media = Media::find($mediaId);
            if ($media && str_starts_with($media['mime_type'], 'image/')) {
                return $media['file_path'];
            }
        }

        return $current;
    }

    private function syncGallery(int $roomId): void
    {
        $images = [];
        $ids = $_POST['gallery_media_ids'] ?? [];
        if (!is_array($ids)) {
            $ids = [];
        }
        foreach ($ids as $mediaId) {
            $media = Media::find((int) $mediaId);
            if ($media && str_starts_with($media['mime_type'], 'image/')) {
                $images[] = [
                    'file_path' => $media['file_path'],
                    'media_id' => (int) $media['id'],
                ];
            }
        }
        RoomImage::syncForRoom($roomId, $images);
    }
}
