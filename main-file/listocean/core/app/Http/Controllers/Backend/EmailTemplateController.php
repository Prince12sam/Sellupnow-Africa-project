<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{

//    use EmailTemplateHelperTrait;
    const BASE_PATH = 'backend.pages.email-template.';
    const BASE_PATH_TWO = 'backend.pages.email-template.admin.';
    const BASE_PATH_JOB = 'backend.pages.email-template.jobs.';
    const BASE_PATH_WALLET = 'backend.pages.email-template.wallet.';

    public function allEmailTemplates(){
        return view(self::BASE_PATH.'all');
    }

    public function globalEmailTemplateSettings(Request $request)
    {

        if($request->isMethod('post')){
            $request->validate([
                'order_mail_success_message' => 'nullable|string',
                'contact_mail_success_message' => 'nullable|string'
            ]);
            $save_data = [
                'order_mail_success_message',
                'contact_mail_success_message',
            ];
            foreach ($save_data as $item) {
                if (empty($request->$item)) {
                    continue;
                }
                update_static_option($item, $request->$item);
            }
            return redirect()->back()->with(FlashMsg::settings_update());

        }
        return view(self::BASE_PATH.'global-email-template');
    }


    public function userRegisterTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_register_subject'=>'required|min:5|max:100',
                'user_register_message'=>'required|min:10|max:1000',
                'user_register_message_for_admin'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'user_register_subject',
                'user_register_message',
                'user_register_message_for_admin',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'user-register-template');
    }

    public function userIdentityVerificationTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_identity_verification_subject'=>'required|min:5|max:100',
                'admin_user_identity_verification_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'user_identity_verification_subject',
                'admin_user_identity_verification_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'identity-verification-template');
    }

    public function userEmailVerifyTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_email_verify_subject'=>'required|min:5|max:100',
                'user_email_verify_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'user_email_verify_subject',
                'user_email_verify_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'user-email-verify-template');
    }


    public function userWalletDepositTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'user_deposit_to_wallet_subject'=>'required|min:5|max:1000',
                'user_deposit_to_wallet_message'=>'required|min:10|max:1000',
                'user_deposit_to_wallet_message_admin'=>'required|min:10',
            ]);
            $fields = [
                'user_deposit_to_wallet_subject',
                'user_deposit_to_wallet_message',
                'user_deposit_to_wallet_message_admin',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_WALLET.'user-wallet');
    }


    public function userNewListingApprovalTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'listing_approve_subject'=>'required|min:5|max:100',
                'listing_approve_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'listing_approve_subject',
                'listing_approve_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'listings.new-listing-approve-template');
    }

    public function userNewListingPublishTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'listing_publish_subject'=>'required|min:5|max:100',
                'listing_publish_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'listing_publish_subject',
                'listing_publish_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'listings.new-listing-publish-template');
    }

    public function userNewListingUnpublishedTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'listing_unpublished_subject'=>'required|min:5|max:100',
                'listing_unpublished_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'listing_unpublished_subject',
                'listing_unpublished_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'listings.new-listing-unpublished-template');
    }

    public function userGuestAddNewListingTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'guest_add_new_listing_subject'=>'required|min:5|max:100',
                'guest_add_new_listing_message'=>'required|min:10|max:1000',
                'guest_add_new_listing_message_for_admin'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'guest_add_new_listing_subject',
                'guest_add_new_listing_message',
                'guest_add_new_listing_message_for_admin',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'listings.guest-add-new-listing-template');
    }

    public function userGuestApproveListingTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'guest_listing_approve_subject'=>'required|min:5|max:100',
                'guest_listing_approve_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'guest_listing_approve_subject',
                'guest_listing_approve_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'listings.guest-listing-approve-template');
    }

    public function userGuestPublishListingTemplate(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'guest_listing_publish_subject'=>'required|min:5|max:100',
                'guest_listing_publish_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'guest_listing_publish_subject',
                'guest_listing_publish_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH.'listings.guest-listing-publish-template');
    }


    public function seller_report(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'seller_report_subject'=>'required|min:5|max:100',
                'seller_report_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'seller_report_subject',
                'seller_report_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'seller-report-template');
    }

    public function seller_payout_request(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'seller_payout_subject'=>'required|min:5|max:100',
                'seller_payout_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'seller_payout_subject',
                'seller_payout_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'seller-payout-request-template');
    }

    public function seller_order_ticket(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'seller_order_ticket_subject'=>'required|min:5|max:100',
                'seller_order_ticket_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'seller_order_ticket_subject',
                'seller_order_ticket_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'seller-order-ticket-template');
    }

    public function seller_verification(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'seller_verification_subject'=>'required|min:5|max:100',
                'seller_verification_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'seller_verification_subject',
                'seller_verification_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'seller-verification-template');
    }

    public function seller_extra_service(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'seller_extra_service_subject'=>'required|min:5|max:100',
                'seller_extra_service_message'=>'required|min:10|max:1000',
                'seller_to_buyer_extra_service_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'seller_extra_service_subject',
                'seller_extra_service_message',
                'seller_to_buyer_extra_service_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'seller-extra-service-template');
    }

    public function buyer_decline(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'buyer_order_decline_subject'=>'required|min:5|max:100',
                'buyer_order_decline_message'=>'required|min:10|max:1000',
                'buyer_to_admin_extra_service_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'buyer_order_decline_subject',
                'buyer_order_decline_message',
                'buyer_to_admin_extra_service_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'buyer-order-complete-decline-template');
    }

    public function buyer_report(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'buyer_report_subject'=>'required|min:5|max:100',
                'buyer_report_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'buyer_report_subject',
                'buyer_report_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'buyer-report-template');
    }

    public function buyer_order_ticket(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'buyer_order_ticket_subject'=>'required|min:5|max:100',
                'buyer_order_ticket_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'buyer_order_ticket_subject',
                'buyer_order_ticket_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'buyer-order-ticket-template');
    }

    public function buyer_extra_service_accept(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'buyer_extra_service_subject'=>'required|min:5|max:100',
                'buyer_extra_service_message'=>'required|min:10|max:1000',
                'buyer_to_seller_extra_service_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'buyer_extra_service_subject',
                'buyer_extra_service_message',
                'buyer_to_seller_extra_service_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));
        }
        return view(self::BASE_PATH.'buyer-extra-service-accept-template');
    }


    //admin email template
    public function change_payment_status(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'admin_change_payment_status_subject'=>'required|min:5|max:100',
                'admin_change_payment_status_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'admin_change_payment_status_subject',
                'admin_change_payment_status_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_TWO.'change-payment-status-template');
    }

    public function withdraw_amount_send(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'admin_withdraw_amount_send_subject'=>'required|min:5|max:100',
                'admin_withdraw_amount_send_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'admin_change_payment_status_subject',
                'admin_withdraw_amount_send_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_TWO.'withdraw-amount-send-template');
    }

    public function service_approve(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'admin_service_approve_subject'=>'required|min:5|max:100',
                'admin_service_approve_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'admin_service_approve_subject',
                'admin_service_approve_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_TWO.'service-approve-template');
    }

    public function service_assign_to_seller(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'admin_service_assign_subject'=>'required|min:5|max:100',
                'admin_service_assign_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'admin_service_assign_subject',
                'admin_service_assign_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_TWO.'admin-service-assign-to-seller-template');
    }

    public function seller_verification_from_admin(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'admin_seller_verification_subject'=>'required|min:5|max:100',
                'admin_seller_verification_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'admin_seller_verification_subject',
                'admin_seller_verification_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_TWO.'seller-verification-template');
    }

    public function user_verification_code(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'admin_user_verification_code_subject'=>'required|min:5|max:100',
                'admin_user_verification_code_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'admin_user_verification_code_subject',
                'admin_user_verification_code_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_TWO.'verification-code-template');
    }

    public function user_new_password(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'admin_user_new_password_subject'=>'required|min:5|max:100',
                'admin_user_new_password_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'admin_user_new_password_subject',
                'admin_user_new_password_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_TWO.'new-password-template');
    }

    public function order_ad_sell_buyer(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'new_order_email_subject'=>'required|min:5|max:100',
                'new_order_buyer_message'=>'required|min:10|max:1000',
                'new_order_admin_seller_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'new_order_email_subject',
                'new_order_buyer_message',
                'new_order_admin_seller_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_TWO.'new-order-template');
    }

    public function job_apply(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'job_apply_subject'=>'required|min:5|max:100',
                'job_apply_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'job_apply_subject',
                'job_apply_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_JOB.'job-apply-template');
    }


    public function job_create(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'job_create_subject'=>'required|min:5|max:100',
                'job_create_message'=>'required|min:10|max:1000',
            ]);
            $fields = [
                'job_create_subject',
                'job_create_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_JOB.'job-create-template');
    }


    public function renew_subscription(Request $request)
    {

        if($request->isMethod('post')){
            $request->validate([
                'renew_subscription_email_subject'=>'required|min:5|max:100',
                'renew_subscription_seller_message'=>'required|min:10|max:1400',
                'renew_subscription_admin_message'=>'required|min:10|max:1400',
            ]);
            $fields = [
                'renew_subscription_email_subject',
                'renew_subscription_seller_message',
                'renew_subscription_admin_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_SUBSCRIPTION.'renew-subscription-template');
    }

    public function subscription_payment_status(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'payment_subscription_email_subject'=>'required|min:5|max:100',
                'payment_subscription_seller_message'=>'required|min:5|max:1000',
            ]);
            $fields = [
                'payment_subscription_email_subject',
                'payment_subscription_seller_message',
            ];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::item_new(__('Update Success')));

        }
        return view(self::BASE_PATH_SUBSCRIPTION.'payment-status-template');
    }

}
