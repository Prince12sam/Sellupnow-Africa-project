import 'dart:developer';

import 'package:carousel_slider/carousel_options.dart';
import 'package:get/get.dart';
import 'package:incodes_payment/incodes_payment_services.dart';
import 'package:listify/custom/custom_web_view/payment_web_view.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/payment/paypal/paypal_package_api.dart';
import 'package:listify/payment/paystack/paystack_package_api.dart';
import 'package:listify/payment/flutter_wave/flutter_wave_services.dart';
import 'package:listify/payment/razor_pay/razor_pay_service.dart';
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

    getUserProfileResponseModel = await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
    Database.getUserProfileResponseModel = getUserProfileResponseModel;
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
      Utils.showToast(Get.context!, 'EnumLocale.txtSelectPaymentMethod.name.tr');
    }
    // if (Database.settingApiResponseModel?.data?.enableRazorpay == true) {
    if (selectedPaymentMethod == 0) {
      await onClickRazorPay(amount, id, packageType);
    }
    // }
    // if (Database.settingApiResponseModel?.data?.enableStripe == true) {
    if (selectedPaymentMethod == 1) {
      await onClickStripe(amount, id, packageType);
    }
    // }
    // if (Database.settingApiResponseModel?.data?.enableFlutterwave == true) {
    if (selectedPaymentMethod == 2) {
      onClickFlutterWave(amount, id, packageType);
    }
    // }
    // if (Database.settingApiResponseModel?.data?.enableGooglePlay == true) {
      if (selectedPaymentMethod == 3) {
        onClickPayStack(amount, id, packageType);
      }
    // }
    // if (Database.settingApiResponseModel?.data?.enableGooglePlay == true) {
      if (selectedPaymentMethod == 5) {
        onClickPayPal(amount, id, packageType);
      }
      // }

    // if (Database.settingApiResponseModel?.data?.enableGooglePlay == true) {
    if (selectedPaymentMethod == 6) {
      onClickInAppPurchase(amount, id, packageType);
    }
    // }
    //
    // if (Database.settingApiResponseModel?.data?.enableGooglePlay == true) {
    if (selectedPaymentMethod == 7) {
      onClickCashFree(amount, id, packageType);
    }
    // }
  }

  /// razor pay
  Future<void> onClickRazorPayOld(num amount, String id, String packageType) async {
    Utils.showLog("Razorpay Payment Working....");

    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...
      final razorKey = Database.settingApiResponseModel?.data?.razorpayKeyId ?? '';
      if (razorKey.isEmpty) {
        Get.back();
        Utils.showToast(Get.context!, "Razorpay is not configured.");
        return;
      }
      RazorPayService().init(
        razorKey: razorKey,
        callback: () async {
          final token = await FirebaseAccessToken.onGet() ?? "";
          final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

          Utils.showLog("RazorPay Payment Successfully");

          Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...

          // purchaseCoinPlan = await PurchaseCoinPlanApi.callApi(coinPlanId: id, paymentGateway: "RazorPay", token: token, uid: uid);
          purchasePlanHistoryResponseModel =
              await PurchasePlanHistoryApi.callApi(packageId: id, paymentGateway: "razorpay", token: token, uid: uid, packageType: packageType);

          Get.back(); // Stop Loading...

          if (purchasePlanHistoryResponseModel?.status == true) {
            getSubscriptionPlan();
            getUserProfileResponseModel = await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
            Database.getUserProfileResponseModel = getUserProfileResponseModel;
            update([Constant.idProfile]);
            Utils.showToast(Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
            update([Constant.idSubscription]);

            Get.back(); // Close Bottom Sheet...
          } else {
            Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
      );
      await 1.seconds.delay();
      RazorPayService().razorPayCheckout((amount * 100).toInt());
      Get.back(); // Stop Loading...
    } catch (e) {
      Get.back(); // Stop Loading...
      Utils.showLog("RazorPay Payment Failed => $e");
    }
  }



  Future<void> onClickRazorPay(num amount, String id, String packageType) async {
    Utils.showLog("Razorpay Payment (Incodes) starting...");

    try {
      // Razorpay key from settings (public key ID)
      final razorKey = Database.settingApiResponseModel?.data?.razorpayKeyId ?? '';
      if (razorKey.isEmpty) {
        Utils.showToast(Get.context!, "Razorpay is not configured.");
        return;
      }
      final email    = Database.getUserProfileResponseModel?.user?.email ?? "test@gmail.com";
      final contact  = (Database.getUserProfileResponseModel?.user?.phoneNumber ??
          Database.getUserProfileResponseModel?.user?.phoneNumber ??
          "+91-1234567890");

      final appName  = EnumLocale.txtAppName.name.tr;

      // Convert ARGB int color -> "#RRGGBB" hex
      String _toHex6(int argb) {
        final hex8 = argb.toRadixString(16).padLeft(8, '0'); // AARRGGBB
        return '#${hex8.substring(2)}'; // RRGGBB
      }
      final colorHex = _toHex6(AppColors.appRedColor.value);

      await IncodesPaymentServices.razorPayPayment(
        razorpayKey: razorKey,
        contactNumber: contact,
        emailId: email,
        amount: amount.toDouble(),                // RUPEES (Incodes અંદર paisa convert કરે છે)
        appName: appName,
        colorCode: colorHex,                      // e.g. "#ff5a5f"
        description: 'Purchase $packageType plan',

        // OPTIONAL: custom toast બતાવવો હોય તો onShowToast આપો

        onPaymentSuccess: () async {
          // ✅ Payment success પછી તમારો existing flow જ
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid   = Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

            purchasePlanHistoryResponseModel = await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "razorpay",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back(); // Stop Loading

            if (purchasePlanHistoryResponseModel?.status == true) {
              // તમારા જ calls
              getSubscriptionPlan();
              getUserProfileResponseModel =
              await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
              Database.getUserProfileResponseModel = getUserProfileResponseModel;

              update([Constant.idProfile]);
              Utils.showToast(Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
              update([Constant.idSubscription]);

              // bottom sheet બંધ હોય તો skip
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

        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },

        onExternalWallet: () {
          Utils.showLog("RazorPay External Wallet selected");
        },
      );
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("RazorPay Payment Failed => $e");
      Utils.showToast(Get.context!, "Unable to start payment.");
    }
  }


  /// stripe payment
  Future<void> onClickStripeOld(num amount, String id, String packageType) async {
    try {
      Utils.showLog("Stripe Payment Working...");

      Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...
      await StripeService().init(isTest: true);
      await 1.seconds.delay();

      StripeService()
          .stripePay(
        amount: (amount * 100).toInt(),
        callback: () async {
          final token = await FirebaseAccessToken.onGet() ?? "";
          final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

          Utils.showLog("Stripe Payment Success Method Called....");

          Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...

          // purchaseCoinPlan = await PurchaseCoinPlanApi.callApi(coinPlanId: id, paymentGateway: "Stripe", token: token, uid: uid);
          purchasePlanHistoryResponseModel =
              await PurchasePlanHistoryApi.callApi(packageId: id, paymentGateway: "Stripe", token: token, uid: uid, packageType: packageType);

          Get.back(); // Stop Loading...

          if (purchasePlanHistoryResponseModel?.status == true) {
            getSubscriptionPlan();
            getUserProfileResponseModel = await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
            Database.getUserProfileResponseModel = getUserProfileResponseModel;
            update([Constant.idProfile]);
            Utils.showToast(Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
            update([Constant.idSubscription]);

            Get.back(); // Close Bottom Sheet...
          } else {
            Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
      )
          .then((value) async {
        Utils.showLog("Stripe Payment Successfully");
      }).catchError((e) {
        Utils.showLog("Stripe Payment Error !!!");
      });
      Get.back();
    } catch (e) {
      Get.back();
      Utils.showLog("Stripe Payment Failed !! => $e");
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
            final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

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
              Database.getUserProfileResponseModel = getUserProfileResponseModel;

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


  /// flutter wave
  Future<void> onClickFlutterWaveOld(num amount, String id, String packageType) async {
    Utils.showLog("Flutter Wave Payment Working....");
    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...
      FlutterWaveService.init(
        context: Get.context!,
        amount: (amount * 100).toString(),
        onPaymentComplete: () async {
          final token = await FirebaseAccessToken.onGet() ?? "";
          final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

          Utils.showLog("Flutter Wave Payment Successfully");

          Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...

          // purchaseCoinPlan = await PurchaseCoinPlanApi.callApi(coinPlanId: id, paymentGateway: "Stripe", token: token, uid: uid);
          purchasePlanHistoryResponseModel =
              await PurchasePlanHistoryApi.callApi(packageId: id, paymentGateway: "RazorPay", token: token, uid: uid, packageType: packageType);

          Get.back();

          if (purchasePlanHistoryResponseModel?.status == true) {
            getSubscriptionPlan();
            getUserProfileResponseModel = await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
            Database.getUserProfileResponseModel = getUserProfileResponseModel;
            update([Constant.idProfile]);

            Utils.showToast(Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
            update([Constant.idSubscription]);

            Get.back();
          } else {
            Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
      );
      update();
      Get.back();
    } catch (e) {
      Get.back();
      Utils.showLog("Flutter Wave Payment Failed => $e");
    }
  }

  Future<void> onClickFlutterWave(num amount, String id, String packageType) async {
    Utils.showLog("Flutterwave Payment (Incodes) starting...");
    try {
      // Start Loading...
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      // Keys / customer info
      final publicKey1 =
          Database.settingApiResponseModel?.data?.flutterwaveKeyId ??
              "FLWPUBK_TEST-cdc51a4df113a91fe33a914eaf8d1c75-X";
      final publicKey =
          "FLWPUBK_TEST-cdc51a4df113a91fe33a914eaf8d1c75-X";

      final currency =
          Database.settingApiResponseModel?.data?.currency?.currencyCode ?? "NGN";

      final customerName =
          Database.getUserProfileResponseModel?.user?.name ?? "User";

      final customerEmail =
          Database.getUserProfileResponseModel?.user?.email ?? "testUser@gmail.com";

      // Close loader right before opening payment sheet (so it doesn't block UI)
      if (Get.isDialogOpen == true) Get.back();

      // IncodesPaymentServices: amount in MAJOR units (no *100)
      await IncodesPaymentServices.flutterWavePayment(
        context: Get.context!,
        publicKey: publicKey,
        currency: currency,
        amount: amount.toString(),
        customerName: customerName,
        customerEmail: customerEmail,

        onPaymentSuccess: () async {
          // SAME success flow as your old method ↓
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false); // Start Loading...

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

            Utils.showLog("Flutter Wave Payment Successfully");

            // NOTE: Keeping your original gateway name ("RazorPay") as-is
            purchasePlanHistoryResponseModel = await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "RazorPay",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back(); // Stop Loading...

            if (purchasePlanHistoryResponseModel?.status == true) {
              getSubscriptionPlan();
              getUserProfileResponseModel =
              await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
              Database.getUserProfileResponseModel = getUserProfileResponseModel;

              update([Constant.idProfile]);
              Utils.showToast(Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
              update([Constant.idSubscription]);

              // Close Bottom Sheet...
              if (Get.isOverlaysOpen) Get.back();
            } else {
              Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
            }
          } catch (e) {
            if (Get.isDialogOpen == true) Get.back();
            Utils.showLog("Flutterwave post-payment error => $e");
            Utils.showToast(Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },

        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },
      );

      // Keep your original trailing calls safe:
      update();
      if (Get.isDialogOpen == true) Get.back(); // Stop Loading if still open
      Utils.showLog("Flutterwave payment flow finished.");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back(); // Stop Loading...
      Utils.showLog("Flutter Wave Payment Failed => $e");
    }
  }

  ///pay stack payment




  Future<void> onClickPayStack(num amount, String id, String packageType) async {
    Utils.showLog("Paystack Payment (Server) starting...");
    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      final token = await FirebaseAccessToken.onGet() ?? "";
      final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

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
        Database.getUserProfileResponseModel = getUserProfileResponseModel;
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


///pay pal payment


  Future<void> onClickPayPal(num amount, String id, String packageType) async {
    Utils.showLog("PayPal Payment (Server) starting...");
    try {
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      final token = await FirebaseAccessToken.onGet() ?? "";
      final uid = Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

      if (token.isEmpty || uid.isEmpty) {
        if (Get.isDialogOpen == true) Get.back();
        Utils.showToast(Get.context!, "Login required.");
        return;
      }

      final createRes = await PaypalPackageApi.createOrder(
        token: token,
        uid: uid,
        packageId: id,
        packageType: packageType,
      );

      if (Get.isDialogOpen == true) Get.back();

      if (createRes == null || createRes["status"] != true) {
        Utils.showToast(Get.context!, createRes?["message"] ?? "Unable to start PayPal payment.");
        return;
      }

      final data = createRes["data"] ?? {};
      final approvalUrl = data["approvalUrl"] ?? "";
      final returnUrl = data["returnUrl"] ?? "";
      final cancelUrl = data["cancelUrl"] ?? "";
      final initOrderId = data["orderId"] ?? "";

      if (approvalUrl.isEmpty || returnUrl.isEmpty) {
        Utils.showToast(Get.context!, "PayPal is not configured.");
        return;
      }

      String? _getQuery(String? url, String key) {
        if (url == null || url.isEmpty) return null;
        return Uri.tryParse(url)?.queryParameters[key];
      }

      final resultUrl = await Get.to(() => PaymentWebView(
            initialUrl: approvalUrl,
            successUrlPrefix: returnUrl,
            cancelUrlPrefix: cancelUrl,
            title: "PayPal",
          ));

      if (resultUrl == null) {
        Utils.showToast(Get.context!, "Payment cancelled.");
        return;
      }

      final orderId = _getQuery(resultUrl, "token") ?? initOrderId;
      if (orderId.isEmpty) {
        Utils.showToast(Get.context!, "Unable to capture PayPal payment.");
        return;
      }

      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      final captureRes = await PaypalPackageApi.captureOrder(
        token: token,
        uid: uid,
        orderId: orderId,
      );
      if (Get.isDialogOpen == true) Get.back();

      if (captureRes != null && captureRes["status"] == true) {
        Utils.showLog("PayPal Payment Successfully");
        getSubscriptionPlan();
        getUserProfileResponseModel =
            await GetUserProfileApi.callApi(loginUserId: Database.loginUserFirebaseId);
        Database.getUserProfileResponseModel = getUserProfileResponseModel;
        update([Constant.idProfile]);
        Utils.showToast(Get.context!, captureRes["message"] ?? "Payment captured.");
        update([Constant.idSubscription]);
        if (Get.isOverlaysOpen) Get.back();
      } else {
        Utils.showToast(Get.context!, captureRes?["message"] ?? "Payment capture failed.");
      }
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("PayPal Payment Failed => $e");
      Utils.showToast(Get.context!, "Unable to start PayPal payment.");
    }
  }


  ///in app purchase payment
  Future<void> onClickInAppPurchase(
      num amount, String id, String packageType) async {
    Utils.showLog("InAppPurchase Payment (Incodes) starting...");
    try {
      // Start Loading...
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      final userId =
          Database.getUserProfileResponseModel?.user?.id.toString() ?? "123456";
      final productId = "com.android.coin100"; // 👉 Tamara productId yaha set karo

      // Loader block ન રહે એ માટે sheet ખોલતા પહેલાં બંધ કરો
      if (Get.isDialogOpen == true) Get.back();

      // ✅ Incodes InAppPurchase integration
      await IncodesPaymentServices.inAppPurchasePayment(
        userId: userId,
        productIds: [productId],
        amount: amount.toDouble(),

        // Success callback
        onPaymentSuccess: () async {
          try {
            Get.dialog(const LoadingWidget(),
                barrierDismissible: false); // Start Loading...

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

            Utils.showLog("InAppPurchase Payment Successfully");

            // NOTE: Gateway name tamaru requirement pramane mukvo
            purchasePlanHistoryResponseModel =
            await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "InAppPurchase",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back(); // Stop Loading...

            if (purchasePlanHistoryResponseModel?.status == true) {
              getSubscriptionPlan();
              getUserProfileResponseModel = await GetUserProfileApi.callApi(
                  loginUserId: Database.loginUserFirebaseId);
              Database.getUserProfileResponseModel = getUserProfileResponseModel;

              update([Constant.idProfile]);
              Utils.showToast(Get.context!,
                  purchasePlanHistoryResponseModel?.message ?? "");
              update([Constant.idSubscription]);

              // Close Bottom Sheet...
              if (Get.isOverlaysOpen) Get.back();
            } else {
              Utils.showToast(
                  Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
            }
          } catch (e) {
            if (Get.isDialogOpen == true) Get.back();
            Utils.showLog("InAppPurchase post-payment error => $e");
            Utils.showToast(Get.context!,
                EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },

        // Failure callback
        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },
      );

      // trailing original calls (safety)
      update();
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("InAppPurchase payment flow finished.");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("InAppPurchase Payment Failed => $e");
    }
  }

///cash free payment

  Future<void> onClickCashFree(
      num amount, String id, String packageType) async {
    Utils.showLog("CashFree Payment (Incodes) starting...");
    try {
      // Start Loading...
      Get.dialog(const LoadingWidget(), barrierDismissible: false);

      final customerName =
          "${Database.getUserProfileResponseModel?.user?.name ?? "John"}";
      final customerEmail =
          Database.getUserProfileResponseModel?.user?.email ?? "john@example.com";
      final customerPhone1 =
          Database.getUserProfileResponseModel?.user?.phoneNumber ?? "9876543210";

      final customerPhone = "9876543210";

      if (Get.isDialogOpen == true) Get.back();

      // ✅ Incodes CashFree integration
      await IncodesPaymentServices.cashFreePayment(
        context: Get.context!,
        clientId: "TEST430329ae80e0f32e41a393d78b923034",
        clientSecret: "TESTaf195616268bd6202eeb3bf8dc458956e7192a85",
        amount: amount.toDouble(),
        currency: "INR",
        customerName: customerName,
        customerEmail: customerEmail,
        customerPhone: customerPhone,
        paymentGatewayName: "Cashfree",

        // Success callback
        onPaymentSuccess: () async {
          try {
            Get.dialog(const LoadingWidget(),
                barrierDismissible: false); // Start Loading...

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

            Utils.showLog("CashFree Payment Successfully");

            purchasePlanHistoryResponseModel =
            await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "Cashfree",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back(); // Stop Loading...

            if (purchasePlanHistoryResponseModel?.status == true) {
              getSubscriptionPlan();
              getUserProfileResponseModel = await GetUserProfileApi.callApi(
                  loginUserId: Database.loginUserFirebaseId);
              Database.getUserProfileResponseModel = getUserProfileResponseModel;

              update([Constant.idProfile]);
              Utils.showToast(Get.context!,
                  purchasePlanHistoryResponseModel?.message ?? "");
              update([Constant.idSubscription]);

              // Close Bottom Sheet...
              if (Get.isOverlaysOpen) Get.back();
            } else {
              Utils.showToast(
                  Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
            }
          } catch (e) {
            if (Get.isDialogOpen == true) Get.back();
            Utils.showLog("CashFree post-payment error => $e");
            Utils.showToast(Get.context!,
                EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },

        // Failure callback
        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },
      );

      // trailing original calls (safety)
      update();
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("CashFree payment flow finished.");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("CashFree Payment Failed => $e");
    }
  }


}
