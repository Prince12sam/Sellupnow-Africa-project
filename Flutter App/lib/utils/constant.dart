import 'package:flutter/material.dart';
import 'package:get_storage/get_storage.dart';
import 'package:listify/utils/app_asset.dart';

class Constant {
  /// =================== Id For Refresh Widgets =================== ///
  static var idOnBoarding = 'idOnBoarding';
  static var idBottomBar = 'idBottomBar';
  static var idViewType = 'idViewType';
  static var idTabChange = 'idTabChange';
  static var switchUpdate = 'switchUpdate';
  static var location = 'location';
  static var idLoginOrSignUp = 'idLoginOrSignUp';
  static var radioButton = 'radioButton';
  static var idResendOtp = 'idResendOtp';
  static var idVerifyOtp = 'idVerifyOtp';
  static var idChangeCountry = 'idChangeCountry';
  static var idProfile = 'idProfile';
  static var idGenderSelect = 'idGenderSelect';
  static var idAcceptTerms = 'idAcceptTerms';
  static var idChangeData = 'idChangeData';
  static var blockList = 'blockList';
  static var adListing = 'adListing';
  static var review = 'review';
  static var idNotification = 'idNotification';
  static var idLocationUpdate = "idLocationUpdate";
  static var idFilterUpdate = "idFilterUpdate";
  static var idFollow = "idFollow";
  static var idUserAds = "idUserAds";
  static var idBanner = "idBanner";
  static var appbar = "appbar";
  static var idCategoryHeader = "idCategoryHeader";
  static var idChangeLanguage = "idChangeLanguage";
  static var idPopularAds = "idPopularAds";
  static var idContact = "idContact";
  static var idAuction = "idAuction";

  /// =================== Id For Refresh API`s Response =================== ///
  static var idLogin = 'idLogin';
  static var idBlog = 'idBlog';
  static var idFaq = 'idFaq';
  static var idSubscription = 'idSubscription';
  static var idSafetyTips = 'idSafetyTips';
  static var idFeatureAdsPlan = 'idFeatureAdsPlan';
  static var idReportReason = 'idReportReason';
  static var idGetCity = 'idGetCity';
  static var idGetCountry = 'idGetCountry';
  static var idPagination = 'idPagination';
  static var idGetState = 'idGetState';
  static var idAllCategory = 'idAllCategory';
  static var idAllAds = 'idAllAds';
  static var idIdentityProof = 'idIdentityProof';
  static var idUserVerification = 'idUserVerification';
  static var idSubscriptionPlan = 'idSubscriptionPlan';
  static var idSendMsg = 'idSendMsg';
  static var idChatList = 'idChatList';
  static var idGetOldChat = 'idGetOldChat';
  static var idPlanChange = 'idPlanChange';
  static var idChangeAudioRecordingEvent = 'onChangeAudioRecordingEvent';
  static var productLike = 'productLike';
  static var productView = 'productView';
  static var idProductDetail = 'idProductDetail';
  static var favPagination = 'favPagination';

  /// =================== Get Storage (Local Storage) =================== ///
  static final storage = GetStorage();

  /// =================== Localization =================== ///
  static const languageEn = "en";
  static const countryCodeEn = "US";

  /// =================== Stripe Merchant =================== ///
  static const stripeMerchantCountryCode = 'IN';

  /// =================== Country Name List =================== ///
  static List countryList = [
    {
      "country": "Arabic",
      "code": "ع",
      "id": "1",
      "image": AppAsset.imPakistan,
    },
    {
      "country": "Bengali",
      "code": "ব",
      "id": "2",
      "image": AppAsset.imIndia,
    },
    {
      "country": "Chinese",
      "code": "中",
      "id": "3",
      "image": AppAsset.imChinese,
    },
    {
      "country": "English",
      "code": "E",
      "id": "4",
      "image": AppAsset.imEnglish,
    },
    {
      "country": "French",
      "code": "F",
      "id": "5",
      "image": AppAsset.imFrench,
    },
    {
      "country": "German",
      "code": "D",
      "id": "6",
      "image": AppAsset.imGerman,
    },
    {
      "country": "Hindi",
      "code": "ह",
      "id": "7",
      "image": AppAsset.imIndia,
    },
    {
      "country": "Italian",
      "code": "B",
      "id": "8",
      "image": AppAsset.imItalian,
    },
    {
      "country": "Indonesian",
      "code": "I",
      "id": "9",
      "image": AppAsset.imIndonesian,
    },
    {
      "country": "Korean",
      "code": "한",
      "id": "10",
      "image": AppAsset.imKorean,
    },
    {
      "country": "Portuguese",
      "code": "P",
      "id": "11",
      "image": AppAsset.imPortuguese,
    },
    {
      "country": "Russian",
      "code": "Р",
      "id": "12",
      "image": AppAsset.imRussian,
    },
    {
      "country": "Spanish",
      "code": "S",
      "id": "13",
      "image": AppAsset.imSpanish,
    },
    {
      "country": "Swahili",
      "code": "S",
      "id": "14",
      "image": AppAsset.imSwahili,
    },
    {
      "country": "Turkish",
      "code": "த",
      "id": "15",
      "image": AppAsset.imTurkish,
    },
    {
      "country": "Telugu",
      "code": "ట",
      "id": "16",
      "image": AppAsset.imIndia,
    },
    {
      "country": "Tamil",
      "code": "T",
      "id": "17",
      "image": AppAsset.imIndia,
    },
    {
      "country": "Urdu",
      "code": "ا",
      "id": "18",
      "image": AppAsset.imPakistan,
    },
  ];

  /// =================== Shimmers =================== ///
  // static Color baseColor = AppColors.shimmerGrey.withValues(alpha: 0.6);
  static Color highlightColor = Colors.grey.withValues(alpha: 0.2);
  static Duration period = const Duration(milliseconds: 500);
}
