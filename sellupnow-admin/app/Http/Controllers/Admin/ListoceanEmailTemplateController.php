<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListoceanEmailTemplateController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function getOption(string $key, string $default = ''): string
    {
        return (string) ($this->listocean()->table('static_options')
            ->where('option_name', $key)
            ->value('option_value') ?? $default);
    }

    private function setOption(string $key, string $value): void
    {
        $this->listocean()->table('static_options')->updateOrInsert(
            ['option_name' => $key],
            ['option_value' => $value, 'updated_at' => now()]
        );
    }

    private function saveFields(Request $request, array $fields): void
    {
        foreach ($fields as $field) {
            $this->setOption($field, (string) ($request->input($field) ?? ''));
        }
    }

    public function index()
    {
        return view('admin.email-templates.index');
    }

    // ── User register ────────────────────────────────────────────────────────
    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'user_register_subject'              => 'required|min:5|max:200',
                'user_register_message'              => 'required|min:10',
                'user_register_message_for_admin'    => 'required|min:10',
            ]);
            $this->saveFields($request, ['user_register_subject', 'user_register_message', 'user_register_message_for_admin']);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.register', [
            'subject'      => $this->getOption('user_register_subject'),
            'message'      => $this->getOption('user_register_message'),
            'adminMessage' => $this->getOption('user_register_message_for_admin'),
        ]);
    }

    // ── Email verify ─────────────────────────────────────────────────────────
    public function emailVerify(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'user_email_verify_subject' => 'required|min:5|max:200',
                'user_email_verify_message' => 'required|min:10',
            ]);
            $this->saveFields($request, ['user_email_verify_subject', 'user_email_verify_message']);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.email-verify', [
            'subject' => $this->getOption('user_email_verify_subject'),
            'message' => $this->getOption('user_email_verify_message'),
        ]);
    }

    // ── Identity verification ────────────────────────────────────────────────
    public function identityVerification(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'user_identity_verification_subject'        => 'required|min:5|max:200',
                'admin_user_identity_verification_message'  => 'required|min:10',
            ]);
            $this->saveFields($request, [
                'user_identity_verification_subject',
                'admin_user_identity_verification_message',
            ]);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.identity-verification', [
            'subject' => $this->getOption('user_identity_verification_subject'),
            'message' => $this->getOption('admin_user_identity_verification_message'),
        ]);
    }

    // ── Wallet deposit ────────────────────────────────────────────────────────
    public function walletDeposit(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'user_deposit_to_wallet_subject'       => 'required|min:5|max:200',
                'user_deposit_to_wallet_message'       => 'required|min:10',
                'user_deposit_to_wallet_message_admin' => 'required|min:10',
            ]);
            $this->saveFields($request, [
                'user_deposit_to_wallet_subject',
                'user_deposit_to_wallet_message',
                'user_deposit_to_wallet_message_admin',
            ]);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.wallet-deposit', [
            'subject'      => $this->getOption('user_deposit_to_wallet_subject'),
            'message'      => $this->getOption('user_deposit_to_wallet_message'),
            'adminMessage' => $this->getOption('user_deposit_to_wallet_message_admin'),
        ]);
    }

    // ── Listing approval ───────────────────────────────────────────────────
    public function listingApproval(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'listing_approve_subject' => 'required|min:5|max:200',
                'listing_approve_message' => 'required|min:10',
            ]);
            $this->saveFields($request, ['listing_approve_subject', 'listing_approve_message']);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.listing-approval', [
            'subject' => $this->getOption('listing_approve_subject'),
            'message' => $this->getOption('listing_approve_message'),
        ]);
    }

    // ── Listing publish ────────────────────────────────────────────────────
    public function listingPublish(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'listing_publish_subject' => 'required|min:5|max:200',
                'listing_publish_message' => 'required|min:10',
            ]);
            $this->saveFields($request, ['listing_publish_subject', 'listing_publish_message']);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.listing-publish', [
            'subject' => $this->getOption('listing_publish_subject'),
            'message' => $this->getOption('listing_publish_message'),
        ]);
    }

    // ── Listing unpublished ────────────────────────────────────────────────
    public function listingUnpublished(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'listing_unpublished_subject' => 'required|min:5|max:200',
                'listing_unpublished_message' => 'required|min:10',
            ]);
            $this->saveFields($request, ['listing_unpublished_subject', 'listing_unpublished_message']);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.listing-unpublished', [
            'subject' => $this->getOption('listing_unpublished_subject'),
            'message' => $this->getOption('listing_unpublished_message'),
        ]);
    }

    // ── Guest add new listing ─────────────────────────────────────────────
    public function guestAddListing(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'guest_add_new_listing_subject'              => 'required|min:5|max:200',
                'guest_add_new_listing_message'              => 'required|min:10',
                'guest_add_new_listing_message_for_admin'    => 'required|min:10',
            ]);
            $this->saveFields($request, [
                'guest_add_new_listing_subject',
                'guest_add_new_listing_message',
                'guest_add_new_listing_message_for_admin',
            ]);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.guest-add-listing', [
            'subject'      => $this->getOption('guest_add_new_listing_subject'),
            'message'      => $this->getOption('guest_add_new_listing_message'),
            'adminMessage' => $this->getOption('guest_add_new_listing_message_for_admin'),
        ]);
    }

    // ── Guest listing approve ─────────────────────────────────────────────
    public function guestApproveListing(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'guest_listing_approve_subject' => 'required|min:5|max:200',
                'guest_listing_approve_message' => 'required|min:10',
            ]);
            $this->saveFields($request, ['guest_listing_approve_subject', 'guest_listing_approve_message']);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.guest-approve-listing', [
            'subject' => $this->getOption('guest_listing_approve_subject'),
            'message' => $this->getOption('guest_listing_approve_message'),
        ]);
    }

    // ── Guest listing publish ─────────────────────────────────────────────
    public function guestPublishListing(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'guest_listing_publish_subject' => 'required|min:5|max:200',
                'guest_listing_publish_message' => 'required|min:10',
            ]);
            $this->saveFields($request, ['guest_listing_publish_subject', 'guest_listing_publish_message']);
            return back()->withSuccess(__('Template updated'));
        }

        return view('admin.email-templates.guest-publish-listing', [
            'subject' => $this->getOption('guest_listing_publish_subject'),
            'message' => $this->getOption('guest_listing_publish_message'),
        ]);
    }
}
