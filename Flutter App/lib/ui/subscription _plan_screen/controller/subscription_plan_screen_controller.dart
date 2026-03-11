import 'dart:developer';

import 'package:carousel_slider/carousel_options.dart';
import 'package:get/get.dart';
import 'package:listify/custom/custom_web_view/payment_web_view.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/payment/paystack/paystack_package_api.dart';
import 'package:listify/payment/stripe/stripe_service.dart';
import 'package:listify/ui/featured_ads_screen/api/purchase_plan_history_api.dart';
import 'package:listify/ui/featured_ads_screen/model/purchase_plan_history_model.dart';
import 'package:listify/ui/login_screen/api/get_user_profile_api.dart';
import 'package:listify/ui/login_screen/model/user_profile_response_model.dart';
import 'package:listify/ui/subscription%20_plan_screen/api/subscription_plan_api.dart';
import 'package:listify/ui/subscription%20_plan_screen/model/subscription_plan_response_model.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/enums.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

class SubscriptionPlanScreenController extends GetxController {
  int currentIndex = 0;
  bool isLoading = false;
  int selectedIndex = -1;
  List<SubscriptionPlan> subscription = [];
  SubscriptionPlanResponseModel? subscriptionPlanResponseModel;
  List<SubscriptionPlan> subscriptionPlan = [];
  int selectedPaymentMethod = -1;
  PurchasePlanHistoryResponseModel? purchasePlanHistoryResponseModel;
  GetUserProfileResponseModel? getUserProfileResponseModel;

  // List image = [
  //   AppAsset.premiumPlanRed,
  //   AppAsset.premiumPlanGrey,
  //   AppAsset.premiumPlanYellow,
  //   AppAsset.premiumPlanBlue,
  // ];

  List<Map<String, dynamic>> imageList = [
    {
      'image': AppAsset.premiumPlanRed,
      'color': AppColors.redColor,
    },
    {
      'image': AppAsset.premiumPlanGrey,
      'color': AppColors.silverColor,
    },
    {
      'image': AppAsset.premiumPlanYellow,
      'color': AppColors.yellow,
    },
    {
      'image': AppAsset.premiumPlanBlue,
      'color': AppColors.blueSubPlan,
    },
  ];

  @override
  void onInit() {
    getSubscriptionPlan();
    super.onInit();
  }

  List<Map<String, dynamic>> dataList = [
    {
      'type': EnumLocale.txtBasicPremiumPlan.name.tr,
      'color': AppColors.redColor,
      'price': '250.0',
      'image': AppAsset.premiumPlanRed,
    },
    {
      'type': EnumLocale.txtSilverPremiumPlan.name.tr,
      'color': AppColors.silverColor,
      'price': '350.0',
      'image': AppAsset.premiumPlanGrey,
    },
    {
      'type': EnumLocale.txtGoldPremiumPlan.name.tr,
      'color': AppColors.yellow,
      'price': '450.0',
      'image': AppAsset.premiumPlanYellow,
    },
    {
      'type': EnumLocale.txtVVIPPremiumPlan.name.tr,
      'color': AppColors.blueSubPlan,
      'price': '550.0',
      'image': AppAsset.premiumPlanBlue,
    },
    {
      'type': EnumLocale.txtBasicPremiumPlan.name.tr,
      'color': AppColors.redColor,
      'price': '250.0',
      'image': AppAsset.premiumPlanRed,
    },
  ];

  ///select plan
  void selectPlan(int index) {
    selectedIndex = index;
    update([Constant.idFeatureAdsPlan]);
  }

  /// change payment method
  void onChangePaymentMethod(int index) async {
    selectedPaymentMethod = index;
    update();

    if (selectedIndex == -1) {
      Utils.showToast(Get.context!, 'Please select a plan first');
      return;
    }

    if (selectedPaymentMethod == -1) {
      Utils.showToast(Get.context!, 'Please select a payment method');
      return;
    }

    final selectedPlan = subscriptionPlan[selectedIndex];
    Utils.showLog(selectedIndex.toString());
    Utils.showLog('selectedPlan.id  ${selectedPlan.id}');
    Utils.showLog('selectedPlan.price  ${selectedPlan.price}');

    onClickPayNow(
      packageType: 'SubscriptionPlan',
      id: selectedPlan.id ?? '',
      amount: selectedPlan.finalPrice ?? 0,
    );

    final profileResult = await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
    if (profileResult != null) {
      getUserProfileResponseModel = profileResult;
      Database.getUserProfileResponseModel = profileResult;
    }
    update([Constant.idProfile]);
  }

  /// get subscription Plan api
  getSubscriptionPlan() async {
    isLoading = true;
    update([Constant.idSubscription]); // notify UI
    subscriptionPlanResponseModel = await SubscriptionPlanApi.callApi();
    subscriptionPlan.clear();
    subscriptionPlan.addAll(subscriptionPlanResponseModel?.data ?? []);

    log("subscriptionPlan list data $subscriptionPlan");

    isLoading = false;
    update([Constant.idSubscription]); // notify UI
  }

  /// slider change
  void onPageChanged(int index, CarouselPageChangedReason reason) {
    currentIndex = index;
    update([Constant.idPlanChange]);
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
      final publishableKey = Database.settingApiResponseModel?.data?.stripePublicKey ?? "";
      await StripeService().init(isTest: true, publishableKey: publishableKey);
      await 600.milliseconds.delay();
      if (Get.isDialogOpen == true) Get.back();

      await StripeService().stripePay(
        amount: (amount * 100).toInt(),
        currency: Database.settingApiResponseModel?.data?.currency?.currencyCode ?? "USD",
        description: "Purchase $packageType plan",
        callback: () async {
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId;

            purchasePlanHistoryResponseModel = await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "Stripe",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back();

            if (purchasePlanHistoryResponseModel?.status == true) {
              getSubscriptionPlan();
              getUserProfileResponseModel =
                  await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
              if (getUserProfileResponseModel != null) {
                Database.getUserProfileResponseModel = getUserProfileResponseModel;
              }

              update([Constant.idProfile]);
              Utils.showToast(Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
              update([Constant.idSubscription]);

              if (Get.isOverlaysOpen) Get.back();
            } else {
              Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
            }
          } catch (e) {
            if (Get.isDialogOpen == true) Get.back();
            Utils.showLog("Post-payment API error => $e");
            Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
      );

      Utils.showLog("Stripe payment flow finished.");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("Stripe Payment Failed !! => $e");
      Utils.showToast(Get.context!, "Unable to start Stripe payment.");
    }
  }


  ///pay stack payment




  Future<void> onClickPayStack(num amount, String id, String packageType) async {
    Utils.showLog("Paystack Payment (Server) starting...");
    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      final token = await FirebaseAccessToken.onGet() ?? "";
      final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? Database.loginUserFirebaseId;

      if (token.isEmpty || uid.isEmpty) {
        if (Get.isDialogOpen == true) Get.back();
        Utils.showToast(Get.context!, "Login required.");
        return;
      }

      final initRes = await PaystackPackageApi.initialize(
        token: token,
        uid: uid,
        packageId: id,
        packageType: packageType,
      );

      if (Get.isDialogOpen == true) Get.back();

      if (initRes == null || initRes["status"] != true) {
        Utils.showToast(Get.context!, initRes?["message"] ?? "Unable to start Paystack payment.");
        return;
      }

      final data = initRes["data"] ?? {};

      // ── Free plan: already activated on server ──
      if (data["free"] == true) {
        Utils.showLog("Free plan activated on server");
        getSubscriptionPlan();
        getUserProfileResponseModel =
            await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
        if (getUserProfileResponseModel != null) {
          Database.getUserProfileResponseModel = getUserProfileResponseModel;
        }
        update([Constant.idProfile]);
        Utils.showToast(Get.context!, initRes["message"] ?? "Plan activated!");
        update([Constant.idSubscription]);
        if (Get.isOverlaysOpen) Get.back();
        return;
      }

      final authorizationUrl = data["authorizationUrl"] ?? "";
      final callbackUrl = data["callbackUrl"] ?? "";
      final initReference = data["reference"] ?? "";

      if (authorizationUrl.isEmpty || callbackUrl.isEmpty) {
        Utils.showToast(Get.context!, "Paystack payment is not configured.");
        return;
      }

      String? _getQuery(String? url, String key) {
        if (url == null || url.isEmpty) return null;
        return Uri.tryParse(url)?.queryParameters[key];
      }

      final resultUrl = await Get.to(() => PaymentWebView(
            initialUrl: authorizationUrl,
            successUrlPrefix: callbackUrl,
            title: "Paystack",
          ));

      if (resultUrl == null) {
        Utils.showToast(Get.context!, "Payment cancelled.");
        return;
      }

      final reference =
          _getQuery(resultUrl, "reference") ?? _getQuery(resultUrl, "trxref") ?? initReference;

      if (reference == null || reference.isEmpty) {
        Utils.showToast(Get.context!, "Unable to verify Paystack payment.");
        return;
      }

      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      final verifyRes = await PaystackPackageApi.verify(
        token: token,
        uid: uid,
        reference: reference,
      );
      if (Get.isDialogOpen == true) Get.back();

      if (verifyRes != null && verifyRes["status"] == true) {
        Utils.showLog("Paystack Payment Successfully");
        getSubscriptionPlan();
        getUserProfileResponseModel =
            await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
        if (getUserProfileResponseModel != null) {
          Database.getUserProfileResponseModel = getUserProfileResponseModel;
        }
        update([Constant.idProfile]);
        Utils.showToast(Get.context!, verifyRes["message"] ?? "Payment verified.");
        update([Constant.idSubscription]);
        if (Get.isOverlaysOpen) Get.back();
      } else {
        Utils.showToast(Get.context!, verifyRes?["message"] ?? "Payment verification failed.");
      }
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("Paystack Payment Failed => $e");
      Utils.showToast(Get.context!, "Unable to start Paystack payment.");
    }
  }
}
