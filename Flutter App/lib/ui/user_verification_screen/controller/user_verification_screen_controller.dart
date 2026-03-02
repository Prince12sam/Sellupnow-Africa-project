import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:listify/custom/dialog/submit_varification_dialog.dart';
import 'package:listify/ui/user_verification_screen/api/id_proof_api.dart';
import 'package:listify/ui/user_verification_screen/api/user_verification_api.dart';
import 'package:listify/ui/user_verification_screen/model/id_proof_response_model.dart';
import 'package:listify/ui/user_verification_screen/model/user_verification_response_model.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/constant.dart';
import 'package:listify/utils/database.dart';
import 'package:listify/utils/utils.dart';

class UserVerificationScreenController extends GetxController {
  final formKey = GlobalKey<FormState>();
  String? dialCode;

  int currentStep = 1;
  var countryCode = "+91";
  var mobileNumberValidate = false.obs;
  final ImagePicker _picker = ImagePicker();
  TextEditingController name = TextEditingController();
  TextEditingController email = TextEditingController();
  TextEditingController number = TextEditingController();
  bool isLoading = false;
  IdProofResponseModel? idProofResponseModel;
  List<IdProof> idProofList = [];
  bool isIdentityExpanded = false;
  IdProof? selectedIdentityProof;
  String? frontImage;
  String? backImage;
  String? selfieImage;
  UserVerificationResponseModel? userVerificationResponseModel;

  @override
  onInit() {
    Utils.showLog("User Profile Response Model: ${Database.getUserProfileResponseModel?.user?.name}");

    name.text = Database.getUserProfileResponseModel?.user?.name ?? '';
    email.text = Database.getUserProfileResponseModel?.user?.email ?? '';
    number.text = Database.getUserProfileResponseModel?.user?.phoneNumber ?? '';
    getIdentityProofApi();
    super.onInit();
  }

  Future<void> nextStep() async {
    log("Attempting to move from Step: $currentStep");

    if (currentStep == 1) {
      String nameText = name.text.trim();
      String emailText = email.text.trim();
      String phoneText = number.text.trim();

      if (nameText.isEmpty || emailText.isEmpty || phoneText.isEmpty) {
        Utils.showToast(Get.context!, "Please fill out all the fields to continue.");

        return;
      }

      if (!GetUtils.isEmail(emailText)) {
        Utils.showToast(Get.context!, "Please enter a valid email address.");

        return;
      }

      currentStep++;
      update();
    }
  }

  void previousStep() {
    if (currentStep > 1) {
      currentStep--;
      update();
    }
  }

  void setStep(int step) {
    currentStep = step;
    update();
  }

  /// pick image

  Future<void> pickFrontImage() async {
    final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      frontImage = image.path;
      update();
    }
  }

  /// pick image
  Future<void> pickBackImage() async {
    final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      backImage = image.path;
      update();
    }
  }

  /// pick selfie image
  Future<void> pickSelfieImage() async {
    final XFile? image = await _picker.pickImage(source: ImageSource.camera);
    if (image != null) {
      selfieImage = image.path;
      update();
    }
  }

  /// remove image

  void removeFrontImage() {
    frontImage = null;
    update();
  }

  /// remove image
  void removeBackImage() {
    backImage = null;
    update();
  }

  /// remove selfie image
  void removeSelfieImage() {
    selfieImage = null;
    update();
  }

  /// getIdentityProof Api

  void getIdentityProofApi() async {
    try {
      isLoading = true;
      update();
      idProofResponseModel = await IdProofApi.callApi();

      //  Store the list of Datum in identityProofList
      idProofList = idProofResponseModel?.data ?? [];

      update([Constant.idIdentityProof]);
    } catch (e, st) {
      log('getIdentityProofApi error: $e\n$st');
    } finally {
      isLoading = false;
      update([Constant.idIdentityProof]);
      log('getIdentityProofApi finally');
    }
  }

  void toggleIdentityExpansion() {
    isIdentityExpanded = !isIdentityExpanded;
    update([Constant.idIdentityProof]);
  }

  void selectIdentityProof(IdProof proof) {
    selectedIdentityProof = proof;
    isIdentityExpanded = false;
    update([Constant.idIdentityProof]);
    // Delay update to allow tile to collapse after tap
    Future.delayed(Duration(milliseconds: 100), () {
      update([Constant.idIdentityProof]);
    });
  }

  String formatVerifyTime(String? dateString) {
    try {
      if (dateString == null || dateString.isEmpty) return "";
      DateTime parsedDate = DateTime.parse(dateString).toLocal();
      return DateFormat("dd MMM yyyy, hh:mm a").format(parsedDate);
    } catch (e) {
      return dateString ?? "";
    }
  }

  Future<void> userVerificationApi() async {
    List<String> identityProofList = [];
    if (frontImage != null && frontImage!.isNotEmpty) {
      identityProofList.add(frontImage.toString());
    }
    if (backImage != null && backImage!.isNotEmpty) {
      identityProofList.add(backImage.toString());
    }

    userVerificationResponseModel = await UserVerificationApi.callApi(
        idProof: selectedIdentityProof?.title ?? '',
        idProofBackPath: backImage.toString(),
        idProofFrontPath: frontImage.toString(),
        selfiePath: selfieImage.toString(),
        uid: Database.getUserProfileResponseModel?.user?.firebaseUid ?? '');
    update([Constant.idUserVerification]);

    Utils.showLog("status::::::::::::$userVerificationResponseModel");

    if (userVerificationResponseModel?.status == true) {
      Database.onSetUserVerify(true);
      Database.onSetUniqueId(userVerificationResponseModel?.data.uniqueId ?? "");
      Database.onSetUniqueId(userVerificationResponseModel?.data.uniqueId ?? "");
      Database.onSetVerifyTime(formatVerifyTime("${userVerificationResponseModel?.data.submittedAt}"));
      Utils.showLog("isVerify::::::::::::::::${Database.isVerify}");
      Utils.showLog("verifyTime::::::::::::::::${Database.verifyTime}");
      Get.dialog(
        barrierDismissible: false,
        barrierColor: AppColors.black.withValues(alpha: 0.8),
        Dialog(
          insetPadding: EdgeInsets.symmetric(horizontal: 32),
          backgroundColor: AppColors.transparent,
          shadowColor: Colors.transparent,
          surfaceTintColor: Colors.transparent,
          elevation: 0,
          child: SubmitVarificationDialog(),
        ),
      );
    } else {
      Database.onSetUserVerify(false);

      Utils.showToast(Get.context!, "Something wrong");
    }
  }
}
