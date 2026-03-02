import 'package:get/get.dart';
import 'package:incodes_payment/incodes_payment_services.dart';
import 'package:listify/custom/custom_web_view/payment_web_view.dart';
import 'package:listify/custom/progress_indicator/progress_dialog.dart';
import 'package:listify/payment/paypal/paypal_package_api.dart';
import 'package:listify/payment/paystack/paystack_package_api.dart';
import 'package:listify/payment/flutter_wave/flutter_wave_services.dart';
import 'package:listify/payment/razor_pay/razor_pay_service.dart';
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
  Future<void> onClickPayNow(
      {required String id,
      required num amount,
      required String packageType}) async {
    if (selectedPaymentMethod == -1) {
      Utils.showToast(
          Get.context!, 'EnumLocale.txtSelectPaymentMethod.name.tr');
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
    // }// if (Database.settingApiResponseModel?.data?.enableGooglePlay == true) {
    if (selectedPaymentMethod == 7) {
      onClickCashFree(amount, id, packageType);
    }
    // }
  }

  /// razor pay
  Future<void> onClickRazorPayOld(
      num amount, String id, String packageType) async {
    Utils.showLog("Razorpay Payment Working....");

    try {
      Get.dialog(const LoadingWidget(),
          barrierDismissible: false); // Start Loading...
      final razorKey =
          Database.settingApiResponseModel?.data?.razorpayKeyId ?? '';
      if (razorKey.isEmpty) {
        Get.back();
        Utils.showToast(Get.context!, "Razorpay is not configured.");
        return;
      }
      RazorPayService().init(
        razorKey: razorKey,
        callback: () async {
          final token = await FirebaseAccessToken.onGet() ?? "";
          final uid =
              Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

          Utils.showLog("RazorPay Payment Successfully");

          Get.dialog(const LoadingWidget(),
              barrierDismissible: false); // Start Loading...

          // purchaseCoinPlan = await PurchaseCoinPlanApi.callApi(coinPlanId: id, paymentGateway: "RazorPay", token: token, uid: uid);
          purchasePlanHistoryResponseModel =
              await PurchasePlanHistoryApi.callApi(
                  packageId: id,
                  paymentGateway: "razorpay",
                  token: token,
                  uid: uid,
                  packageType: packageType);

          Get.back(); // Stop Loading...

          if (purchasePlanHistoryResponseModel?.status == true) {
            getFeaturedAdsPlan();
            Utils.showToast(
                Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
            update([Constant.idFeatureAdsPlan]);

            Get.back(); // Close Bottom Sheet...
            Get.toNamed(AppRoutes.featuredAdsShowScreen);
          } else {
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
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

  Future<void> onClickRazorPay(
    num amount,
    String id,
    String packageType,
  ) async {
    Utils.showLog("Razorpay Payment (Incodes) starting...");

    try {
      final razorKey =
          Database.settingApiResponseModel?.data?.razorpayKeyId ?? '';
      if (razorKey.isEmpty) {
        Utils.showToast(Get.context!, "Razorpay is not configured.");
        return;
      }
      final email =
          Database.getUserProfileResponseModel?.user?.email ?? "test@gmail.com";

      final contact =
          (Database.getUserProfileResponseModel?.user?.phoneNumber ??
              Database.getUserProfileResponseModel?.user?.phoneNumber ??
              "+91-0000000000");

      String _toHex6(int argb) {
        final hex8 = argb.toRadixString(16).padLeft(8, '0');
        return '#${hex8.substring(2)}';
      }

      final appName = EnumLocale.txtAppName.name.tr;
      final hexColor = _toHex6(AppColors.appRedColor.value);

      await IncodesPaymentServices.razorPayPayment(
        razorpayKey: razorKey,
        contactNumber: contact,
        emailId: email,
        amount: amount.toDouble(),
        appName: appName,
        colorCode: hexColor,
        description: 'Purchase $packageType plan',
        onPaymentSuccess: () async {
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);
            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

            purchasePlanHistoryResponseModel =
                await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "razorpay",
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

  /// stripe
  Future<void> onClickStripeOld(
      num amount, String id, String packageType) async {
    try {
      Utils.showLog("Stripe Payment Working...");

      Get.dialog(const LoadingWidget(),
          barrierDismissible: false); // Start Loading...
      await StripeService().init(isTest: true);
      await 1.seconds.delay();

      StripeService()
          .stripePay(
        amount: (amount * 100).toInt(),
        callback: () async {
          final token = await FirebaseAccessToken.onGet() ?? "";
          final uid =
              Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

          Utils.showLog("Stripe Payment Success Method Called....");

          Get.dialog(const LoadingWidget(),
              barrierDismissible: false); // Start Loading...

          // purchaseCoinPlan = await PurchaseCoinPlanApi.callApi(coinPlanId: id, paymentGateway: "Stripe", token: token, uid: uid);
          purchasePlanHistoryResponseModel =
              await PurchasePlanHistoryApi.callApi(
                  packageId: id,
                  paymentGateway: "Stripe",
                  token: token,
                  uid: uid,
                  packageType: packageType);

          Get.back(); // Stop Loading...

          if (purchasePlanHistoryResponseModel?.status == true) {
            getFeaturedAdsPlan();

            Utils.showToast(
                Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
            update([Constant.idFeatureAdsPlan]);

            Get.back(); // Close Bottom Sheet...
            Get.toNamed(AppRoutes.featuredAdsShowScreen);
          } else {
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
      )
          .then((value) async {
        Utils.showLog("Stripe Payment Successfully");
      }).catchError((e) {
        Utils.showLog("Stripe Payment Error !!!");
      });
      Get.back(); // Stop Loading...
    } catch (e) {
      Get.back(); // Stop Loading...
      Utils.showLog("Stripe Payment Failed !! => $e");
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
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

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

  /// flutter wave
  Future<void> onClickFlutterWaveOld(
      num amount, String id, String packageType) async {
    Utils.showLog("Flutter Wave Payment Working....");
    try {
      Get.dialog(const LoadingWidget(),
          barrierDismissible: false); // Start Loading...
      FlutterWaveService.init(
        context: Get.context!,
        amount: (amount * 100).toString(),
        onPaymentComplete: () async {
          final token = await FirebaseAccessToken.onGet() ?? "";
          final uid =
              Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

          Utils.showLog("Flutter Wave Payment Successfully");

          Get.dialog(const LoadingWidget(),
              barrierDismissible: false); // Start Loading...

          // purchaseCoinPlan = await PurchaseCoinPlanApi.callApi(coinPlanId: id, paymentGateway: "Stripe", token: token, uid: uid);
          purchasePlanHistoryResponseModel =
              await PurchasePlanHistoryApi.callApi(
                  packageId: id,
                  paymentGateway: "RazorPay",
                  token: token,
                  uid: uid,
                  packageType: packageType);

          Get.back(); // Stop Loading...

          if (purchasePlanHistoryResponseModel?.status == true) {
            getFeaturedAdsPlan();

            Utils.showToast(
                Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
            update([Constant.idFeatureAdsPlan]);

            Get.back(); // Close Bottom Sheet...
            Get.toNamed(AppRoutes.featuredAdsShowScreen);
          } else {
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
      );
      update();
      Get.back(); // Stop Loading...
    } catch (e) {
      Get.back(); // Stop Loading...
      Utils.showLog("Flutter Wave Payment Failed => $e");
    }
  }

  Future<void> onClickFlutterWave(
      num amount, String id, String packageType) async {
    Utils.showLog("Flutterwave Payment (Incodes) starting...");
    try {
      // Old UX: loader before opening sheet
      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      await 400.milliseconds.delay();
      if (Get.isDialogOpen == true) Get.back();

      // Keys / customer info
      final settingsKey =
          Database.settingApiResponseModel?.data?.flutterwaveKeyId;
      final publicKey = (settingsKey != null && settingsKey.isNotEmpty)
          ? settingsKey
          : "FLWPUBK_TEST-cdc51a4df113a91fe33a914eaf8d1c75-X";

      final currency =
          Database.settingApiResponseModel?.data?.currency?.currencyCode ??
              "NGN";
      final customerName =
          Database.getUserProfileResponseModel?.user?.name ?? "User";
      final customerEmail = Database.getUserProfileResponseModel?.user?.email ??
          "testUser@gmail.com";

      // amount should be MAJOR units string (no *100)
      await IncodesPaymentServices.flutterWavePayment(
        context: Get.context!,
        publicKey: publicKey,
        currency: currency,
        amount: amount.toString(),
        customerName: customerName,
        customerEmail: customerEmail,
        onPaymentSuccess: () async {
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

            // તમારા original success flow (gateway name તમે હવે "Flutterwave" રાખવા માંગતા હો તો તે જ)
            purchasePlanHistoryResponseModel =
                await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "Flutterwave",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back(); // stop loading

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
            Utils.showLog("Flutterwave post-payment error => $e");
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },
      );

      Utils.showLog("Flutterwave payment flow finished.");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("Flutterwave Payment Failed => $e");
      Utils.showToast(Get.context!, "Unable to start Flutterwave payment.");
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

      final currency = "NGN";

      final int majorAmount = amount.toInt();

      // Open Paystack checkout
      await IncodesPaymentServices.payStackPayment(
        context: Get.context!,
        secretKey: publicKey,
        customerEmail: customerEmail,
        amount: majorAmount, // major (e.g. ₦200 => 200)
        currency: currency,

        onPaymentSuccess: () async {
          // ✅ SAME post-payment flow you had in Flutterwave method (featured ads)
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

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


  ///pay pal payment
  Future<void> onClickPayPal(num amount, String id, String packageType) async {
    Utils.showLog("PayPal Payment (Incodes) starting...");
    try {
      // Old UX: loader before opening sheet
      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      await 400.milliseconds.delay();
      if (Get.isDialogOpen == true) Get.back();

      final customerEmail =
          Database.getUserProfileResponseModel?.user?.email ?? "testuser@gmail.com";
      final currency = "USD"; // PayPal mostly USD

      final paypalClientId =
          Database.settingApiResponseModel?.data?.paypalClientId ?? "";
      final paypalSecretKey = "";

      if (paypalClientId.isEmpty || paypalSecretKey.isEmpty) {
        Utils.showToast(Get.context!, "PayPal is not configured.");
        return;
      }

      // Open PayPal checkout
      await IncodesPaymentServices.paypalPayment(
        context: Get.context!,
        clientId: paypalClientId,
        secretKey: paypalSecretKey,
        transactions: [
          {
            "amount": {
              "total": amount.toString(), // dynamic amount
              "currency": currency,
              "details": {
                "subtotal": amount.toString(),
                "shipping": '0',
                "shipping_discount": 0
              },
            },
            "description": "Subscription purchase via PayPal",
            "item_list": {
              "items": [
                {
                  "name": "Subscription Plan",
                  "quantity": 1,
                  "price": amount.toString(),
                  "currency": currency,
                },
              ],
            },
          },
        ],

        onPaymentSuccess: () async {
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

            purchasePlanHistoryResponseModel =
            await PurchasePlanHistoryApi.callApi(
              packageId: id,
              paymentGateway: "PayPal",
              token: token,
              uid: uid,
              packageType: packageType,
            );

            Get.back(); // Stop Loading...

            if (purchasePlanHistoryResponseModel?.status == true) {
              getFeaturedAdsPlan();
              Get.find<FeaturedAdsShowScreenController>().promoteAdsApi();
              Utils.showToast(
                  Get.context!, purchasePlanHistoryResponseModel?.message ?? "");
              update([Constant.idFeatureAdsPlan]);

              if (Get.isOverlaysOpen) Get.back();
              Get.back();
            } else {
              Utils.showToast(
                  Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
            }
          } catch (e) {
            if (Get.isDialogOpen == true) Get.back();
            Utils.showLog("PayPal post-payment error => $e");
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },

        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },
      );

      Utils.showLog("PayPal payment flow finished.");
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
      // Loader show karo
      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      await 400.milliseconds.delay();
      if (Get.isDialogOpen == true) Get.back();

      final productId = "com.android.coin100"; // Tamara product ID yaha mukva
      final userId =
          Database.getUserProfileResponseModel?.user?.id.toString() ?? "123456";

      await IncodesPaymentServices.inAppPurchasePayment(
        userId: userId,
        productIds: [productId],
        amount: amount.toDouble(),
        onPaymentSuccess: () async {
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

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
            Utils.showLog("InAppPurchase post-payment error => $e");
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },
        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },
      );

      Utils.showLog("InAppPurchase payment flow finished.");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("InAppPurchase Payment Failed => $e");
      Utils.showToast(Get.context!, "Unable to start InAppPurchase payment.");
    }
  }

///cash free payment
Future<void> onClickCashFree(
      num amount, String id, String packageType) async {
    Utils.showLog("CashFree Payment (Incodes) starting...");
    try {
      // Loader show karo
      Get.dialog(const LoadingWidget(), barrierDismissible: false);
      await 400.milliseconds.delay();
      if (Get.isDialogOpen == true) Get.back();

      final customerName =
          Database.getUserProfileResponseModel?.user?.name ?? "John";
      final customerEmail =
          Database.getUserProfileResponseModel?.user?.email ?? "john@example.com";
      final customerPhone1 =
          Database.getUserProfileResponseModel?.user?.phoneNumber ?? "9876543210";

      final customerPhone = "9876543210";

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

        // ✅ Success callback
        onPaymentSuccess: () async {
          try {
            Get.dialog(const LoadingWidget(), barrierDismissible: false);

            final token = await FirebaseAccessToken.onGet() ?? "";
            final uid =
                Database.getUserProfileResponseModel?.user?.firebaseUid ?? "";

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
            Utils.showLog("CashFree post-payment error => $e");
            Utils.showToast(
                Get.context!, EnumLocale.txtSomeThingWentWrong.name.tr);
          }
        },

        // ❌ Failure callback
        onPaymentFailure: () {
          Utils.showToast(Get.context!, "Payment failed. Please try again.");
        },
      );

      Utils.showLog("CashFree payment flow finished.");
    } catch (e) {
      if (Get.isDialogOpen == true) Get.back();
      Utils.showLog("CashFree Payment Failed => $e");
      Utils.showToast(Get.context!, "Unable to start CashFree payment.");
    }
  }

}
