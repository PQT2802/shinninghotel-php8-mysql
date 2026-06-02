<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\ContactMessage;
use App\Models\Setting;

class ContactController extends Controller
{
    public function index(): void
    {
        $settings = Setting::allKeyed();
        $this->view('web/contact/index', [
            'title' => 'Contact Us',
            'metaDescription' => 'Contact ' . brand_name() . ' Hotel — reservations, events, and guest services.',
            'breadcrumbs' => [['label' => 'Contact', 'url' => url('/contact')]],
            'settings' => $settings,
            'old' => Session::get('old_input') ?? [],
        ]);
    }

    public function store(): void
    {
        $input = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'message' => trim($_POST['message'] ?? ''),
        ];

        $validator = new Validator($input, [
            'name' => 'required|max:120',
            'email' => 'required|email|max:180',
            'phone' => 'max:40',
            'subject' => 'max:200',
            'message' => 'required|max:5000',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Session::set('old_input', $input);
            $this->redirect(url('/contact'));
        }

        ContactMessage::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'subject' => $input['subject'],
            'message' => $input['message'],
        ]);

        Session::forget('old_input');
        Session::flash('success', __('contact.success'));
        $this->redirect(url('/contact'));
    }
}
