import 'package:get/get.dart';
import 'package:incodes_payment/incodes_payment_services.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/payment/paystack/paystack_package_api.dart';
import 'package:listify/payment/stripe/stripe_service.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/featured_ads_screen/api/featured_ads_plan_api.dart';
import 'package:listify/ui/featured_ads_screen/api/purchase_plan_history_api.dart';
import 'package:listify/ui/featured_ads_screen/controller/featured_ads_show_screen_controller.dart';
import 'package:listify/ui/featured_ads_screen/model/featured_ads_plan_response_model.dart';
import 'package:listify/ui/featured_ads_screen/model/purchase_plan_history_model.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class FeaturedAdsScreenController extends GetxController {
  int selectedIndex = -1;
  bool isLoading = false;
  FeaturedAdsPlanResponseModel? featuredAdsPlanResponseModel;
  List<Datum> featuredAdsPlan = [];
  int selectedPaymentMethod = -1;
  PurchasePlanHistoryResponseModel? purchasePlanHistoryResponseModel;

  @override
  void onInit() {
    getFeaturedAdsPlan();
    super.onInit();
  }

  ///select plan
  void selectPlan(int index) {
    selectedIndex = index;
    update([Constant.idFeatureAdsPlan]);
  }

  /// get featured ads plan
  getFeaturedAdsPlan() async {
    isLoading = true;
    update([Constant.idFeatureAdsPlan]);
    featuredAdsPlanResponseModel = await FeaturedAdsPlanApi.callApi();
    featuredAdsPlan.clear();
    featuredAdsPlan.addAll(featuredAdsPlanResponseModel?.data ?? []);

    Utils.showLog("Featured ads list data $featuredAdsPlan");

    isLoading = false;
    update([Constant.idFeatureAdsPlan]);
  }

  /// on refresh
  onRefresh() async {
    await getFeaturedAdsPlan();
  }

  /// payment
  /// change payment method
  void onChangePaymentMethod(int index) async {
    selectedPaymentMethod = index;
    update();

    if (selectedIndex == -1) {
      Utils.showToast(Get.context!, 'Please select a plan first');
      return;
    }

    final selectedPlan = featuredAdsPlan[selectedIndex];
    Utils.showLog(selectedIndex.toString());
    Utils.showLog('selectedPlan.id  ${selectedPlan.id}');
    Utils.showLog('selectedPlan.price  ${selectedPlan.price}');

    onClickPayNow(
      packageType: 'FeatureAdPackage',
      id: selectedPlan.id ?? '',
      amount: selectedPlan.finalPrice ?? 0,
    );
  }

  /// payment method condition
  Future<void> onClickPayNow({required String id, required num amount, required String packageType}) async {
    if (selectedPaymentMethod == -1) {
      Utils.showToast(Get.context!, EnumLocale.txtSelectPaymentMethod.name.tr);
      return;
    }
    if (selectedPaymentMethod == 0) {
      await onClickStripe(amount, id, packageType);
    }
    if (selectedPaymentMethod == 1) {
      await onClickPayStack(amount, id, packageType);
    }
  }

  Future<void> onClickStripe(num amount, String id, String packageType) async {
    try {
      Utils.showLog("Stripe Payment starting...");

      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      final publishableKey =
          Database.settingApiResponseModel?.data?.stripePublicKey ?? "";
      await StripeService().init(isTest: true, publishableKey: publishableKey);
      await 600.milliseconds.delay();
      if (Get.isDialogOpen == true) Get.back();

      await StripeService().stripePay(
        amount: (amount * 100).toInt(),
        currency:
            Database.settingApiResponseModel?.data?.currency?.currencyCode ??
                "USD",
        description: "Purchase $packageType plan",
        callback: () async {
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId;

            purchasePlanHistoryResponseModel =
                await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "Stripe",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back();

            if (purchasePlanHistoryResponseModel?.status == true) {
              getFeaturedAdsPlan();
              Get.find<FeaturedAdsShowScreenController>().promoteAdsApi();
              Utils.showToast(Get.context!,
                  purchasePlanHistoryResponseModel?.message ?? "");
              update([Constant.idFeatureAdsPlan]);

              if (Get.isOverlaysOpen) Get.back();
              Get.back();
            } else {
              Utils.showToast(
                  Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
            }
          } catch (e) {
            if (Get.isDialogOpen == true) Get.back();
            Utils.showLog("Post-payment API error => $e");
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
      );

      Utils.showLog("Stripe payment flow finished (returned).");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("Stripe Payment Failed !! => $e");
      Utils.showToast(Get.context!, "Unable to start Stripe payment.!!!!!!!!!");
    }
  }

  ///pay stack payment

  Future<void> onClickPayStack(
      num amount, String id, String packageType) async {
    Utils.showLog("Paystack Payment (Incodes) starting...");
    try {
      // Old UX: loader before opening sheet
      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      await 400.milliseconds.delay();
      if (Get.isDialogOpen == true) Get.back();

      // --- Keys / customer info ---
      final publicKey =
          Database.settingApiResponseModel?.data?.paystackPublicKey ?? "";
      if (publicKey.isEmpty) {
        if (Get.isDialogOpen == true) Get.back();
        Utils.showToast(Get.context!, "Paystack is not configured.");
        return;
      }

      final customerEmail = Database.getUserProfileResponseModel?.user?.email ??
          "testuser@gmail.com";

      final currency = Database.settingApiResponseModel?.data?.currency?.currencyCode ?? "GHS";

      final int majorAmount = amount.toInt();

      // Open Paystack checkout
      await IncodesPaymentServices.payStackPayment(
        context: Get.context!,
        secretKey: publicKey,
        customerEmail: customerEmail,
        amount: majorAmount,
        currency: currency,

        onPaymentSuccess: () async {
          // ✅ SAME post-payment flow you had in Flutterwave method (featured ads)
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId;

            purchasePlanHistoryResponseModel =
                await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "Paystack",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back(); // Stop Loading...

            if (purchasePlanHistoryResponseModel?.status == true) {
              getFeaturedAdsPlan();
              Get.find<FeaturedAdsShowScreenController>().promoteAdsApi();
              Utils.showToast(Get.context!,
                  purchasePlanHistoryResponseModel?.message ?? "");
              update([Constant.idFeatureAdsPlan]);

              // Close Bottom Sheet if open
              if (Get.isOverlaysOpen) Get.back();Get.back();
            } else {
              Utils.showToast(
                  Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
            }
          } catch (e) {
            if (Get.isDialogOpen == true) Get.back();
            Utils.showLog("Paystack post-payment error => $e");
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },

        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },
      );

      Utils.showLog("Paystack payment flow finished.");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("Paystack Payment Failed => $e");
      Utils.showToast(Get.context!, "Unable to start Paystack payment.");
    }
  }
}
