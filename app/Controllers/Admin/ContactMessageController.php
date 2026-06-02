<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('content.manage');
        $status = $_GET['status'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pager = ContactMessage::paginate($page, 20, $status ?: null);

        $this->view('admin/contact/index', [
            'title' => 'Contact Messages',
            'messages' => $pager['data'],
            'pager' => $pager,
            'statusFilter' => $status,
            'unreadCount' => ContactMessage::countUnread(),
        ]);
    }

    public function show(int $id): void
    {
        $this->requirePermission('content.manage');
        $message = ContactMessage::find($id);
        if (!$message) {
            Session::flash('error', 'Message not found.');
            $this->redirect(url('/admin/contact-messages'));
        }
        if ($message['status'] === 'unread') {
            ContactMessage::markRead($id);
            $message['status'] = 'read';
        }
        $this->view('admin/contact/show', [
            'title' => 'Message from ' . $message['name'],
            'message' => $message,
        ]);
    }

    public function markRead(int $id): void
    {
        $this->requirePermission('content.manage');
        ContactMessage::markRead($id);
        Session::flash('success', 'Marked as read.');
        $this->redirect(url('/admin/contact-messages'));
    }

    public function markUnread(int $id): void
    {
        $this->requirePermission('content.manage');
        ContactMessage::markUnread($id);
        Session::flash('success', 'Marked as unread.');
        $this->redirect(url('/admin/contact-messages/show/' . $id));
    }

    public function delete(int $id): void
    {
        $this->requirePermission('content.manage');
        ContactMessage::delete($id);
        Session::flash('success', 'Message deleted.');
        $this->redirect(url('/admin/contact-messages'));
    }
}
