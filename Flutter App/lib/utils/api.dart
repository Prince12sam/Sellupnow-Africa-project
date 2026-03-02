abstract class Api {
  /// server url
  static const baseUrl = String.fromEnvironment(
    'API_BASE_URL',
        defaultValue: "http://127.0.0.1:8098/",
  );
  static const secretKey = String.fromEnvironment(
    'API_SECRET_KEY',
    defaultValue: "",
  );

  // >>>>> >>>>> Login Page Api <<<<< <<<<<
  static const checkUserExit = "${baseUrl}api/client/user/verifyUserExistence?";
  static const login = "${baseUrl}api/client/user/loginOrSignupUser";
  static const getLoginUserProfile =
      "${baseUrl}api/client/user/fetchUserProfile";
  static const editProfile = "${baseUrl}api/client/user/updateProfileInfo";

  // >>>>>>>>>>>>>>> >>>>>>>>>> Blog Api >>>>>>>>>>>> >>>>>>>>>>>>>>  //
  static const blog = "${baseUrl}api/client/blog/retrieveBlogList";
  static const trendingBlog =
      "${baseUrl}api/client/blog/retrieveTrendingBlogPosts";
  static const blogById = "${baseUrl}api/client/blog/retrieveBlogPost";

  // >>>>>>>>>>>>>>>>>> >>>>>>>>> FAQ Api >>>>>>>> >>>>>>>>>>>>>>>> //
  static const faqApi = "${baseUrl}api/client/faq/retrieveFAQList";

  //  >>>>>>>>>>>>>>>>>>>>>>> >>>>>>>>> subscription plan api  >>>>>>>>> >>>>>>>>> //
  static const subscriptionPlanApi =
      "${baseUrl}api/client/subscriptionPlan/fetchSubscriptionPlans";

  //  >>>>>>>>>>>>>>>>>>>>>>> >>>>>>>>> Safety Tips api  >>>>>>>>> >>>>>>>>> //
  static const safetyTipsApi = "${baseUrl}api/client/tip/listHelpfulHints?";

  //  >>>>>>>>>>>>>>>>>>>>>>> >>>>>>>>> Featured Ads api  >>>>>>>>> >>>>>>>>> //
  static const featuredAdsPlanApi =
      "${baseUrl}api/client/featureAdPackage/fetchFeaturedAdPackages?";

  //  >>>>>>>>>>>>>>>>>>>>>>> >>>>>>>>> Report reason api  >>>>>>>>> >>>>>>>>> //
  static const reportReasonApi =
      "${baseUrl}api/client/reportReason/fetchReportReasons";

  //  >>>>>>>>>>>>>>>>>>>>>>> >>>>>>>>> home screen banner api  >>>>>>>>> >>>>>>>>> //
  static const bannerApi = "${baseUrl}api/client/banner/retrieveBannerList";

  // >>>>>>>>>>>>>>>>>>>>>>> >>>>>>>>> city country state api  >>>>>>>>> >>>>>>>>> //
  static const cityApi = "${baseUrl}api/client/city/fetchCityList?";
  static const countryApi = "${baseUrl}api/client/country/fetchCountryList?";
  static const stateApi = "${baseUrl}api/client/state/fetchStateList?";

  // >>>>>>>>>>>>>>>>>>>>>>> >>>>>>>>> category api  >>>>>>>>> >>>>>>>>> //
  static const categoryApi =
      "${baseUrl}api/client/category/retrieveCategoryList";
  static const subCategoryApi =
      "${baseUrl}api/client/category/fetchSubcategoriesByParent?";
  static const hierarchicalCategoryApi =
      "${baseUrl}api/client/category/getHierarchicalCategories";

  // >>>>>>>> >>>>>>>>>>>>>> user verification >>>>>>>>>>> >>>>>>>>>>>> //
  static const userVerificationApi =
      "${baseUrl}api/client/verification/submitUserVerification";

  // >>>>>>>> >>>>>>>>>>>>>> setting Api >>>>>>>>>>> >>>>>>>>>>>> //
  static const settingApi = "${baseUrl}api/client/setting/retrieveSystemConfig";

  // >>>>>>>> >>>>>>>>>>>>>> listing Api / my ads api >>>>>>>>>>> >>>>>>>>>>>> //
  static const addListingApi = "${baseUrl}api/client/adListing/createAdListing";
  static const allAddList =
      "${baseUrl}api/client/adListing/fetchAdListingRecords?";

  // >>>>>>>> >>>>>>>>>>>>>> AI Listing Assistant >>>>>>>>>>> >>>>>>>>>>>> //
  static const aiListingAssistApi =
      "${baseUrl}api/client/aiListing/suggestListingContent";

  // product
  static const popularProduct =
      "${baseUrl}api/client/adListing/fetchPopularAdListingRecords";
  static const relatedProductApi =
      "${baseUrl}api/client/adListing/fetchAdsByRelatedCategory?";
  static const addLikeProduct = "${baseUrl}api/client/adLike/toggleAdLike?";

  // >>>>>>>> >>>>>>>>>>>>>> user verification >>>>>>>>>>> >>>>>>>>>>>> //
  static const categoryAttribute =
      "${baseUrl}api/client/attributes/fetchCategoryAttributes?";

  // >>>>>>>> >>>>>>>>>>>>>> purchase history >>>>>>>>>>> >>>>>>>>>>>> //
  static const purchaseHistory =
      "${baseUrl}api/client/purchaseHistory/createPurchaseHistory";
  static const getPurchaseHistory =
      "${baseUrl}api/client/purchaseHistory/getPurchaseHistory?";

  // >>>>>>>> >>>>>>>>>>>>>> Paystack / PayPal package payments >>>>>>>>>>> //
  static const paystackInitPackage =
      "${baseUrl}api/client/paystack/initialize-package-payment";
  static const paystackVerifyPackage =
      "${baseUrl}api/client/paystack/verify-package-payment";
  static const paypalCreateOrder =
      "${baseUrl}api/client/paypal/create-order";
  static const paypalCaptureOrder =
      "${baseUrl}api/client/paypal/capture-order";

  // >>>>>>>> >>>>>>>>>>>>>> purchase history >>>>>>>>>>> >>>>>>>>>>>> //
  static const idProofApi = "${baseUrl}api/client/idProof/listIdProofs";

  static const categoryWiseProduct =
      "${baseUrl}api/client/adListing/fetchCategoryWiseAdListings";

  // >>>>>>>> >>>>>>>>>>>>>> chat >>>>>>>>>>> >>>>>>>>>>>> //
  static const chatList = "${baseUrl}api/client/chatTopic/getChatList?";
  static const chatHistory = "${baseUrl}api/client/chat/getChatHistory?";

  static const ipApi = "http://ip-api.com/json";

  // >>>>>>>> >>>>>>>>>>>>>> upload video >>>>>>>>>>> >>>>>>>>>>>> //
  static const uploadAdVideoApi = "${baseUrl}api/client/adVideo/uploadAdVideo";
  static const userVideoListApi = "${baseUrl}api/client/adVideo/getAdVideos?";
  static const specificSellerVideoListApi =
      "${baseUrl}api/client/adVideo/getAdVideosOfSeller?";
  static const userVideoDeleteApi =
      "${baseUrl}api/client/adVideo/deleteAdVideo?";
  static const likeVideoApi =
      "${baseUrl}api/client/adVideoLike/toggleAdVideoLike?";

  // >>>>>>>> >>>>>>>>>>>>>> Chat >>>>>>>>>>> >>>>>>>>>>>> //
  static const sendImageAudioApi = "${baseUrl}api/client/chat/sendChatMessage";

  // >>>>>>>> >>>>>>>>>>>>>> delete user account >>>>>>>>>>> >>>>>>>>>>>> //
  static const deleteUserAccount =
      "${baseUrl}api/client/user/deactivateAccount";

  // >>>>>>>> >>>>>>>>>>>>>> remove product  >>>>>>>>>>> >>>>>>>>>>>> //
  static const removeProductApi =
      "${baseUrl}api/client/adListing/removeAdListing";

  // >>>>>>>> >>>>>>>>>>>>>> update product  >>>>>>>>>>> >>>>>>>>>>>> //
  static const updateListingApi =
      "${baseUrl}api/client/adListing/updateAdListing";

  // >>>>>>>> >>>>>>>>>>>>>> ad view  >>>>>>>>>>> >>>>>>>>>>>> //
  static const adViewApi = "${baseUrl}api/client/adView/recordAdView";

  // >>>>>>>> >>>>>>>>>>>>>> Block user api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const blockUserApi = "${baseUrl}api/client/block/toggleBlockUser";

  // >>>>>>>> >>>>>>>>>>>>>> report user api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const reportUserApi = "${baseUrl}api/client/report/reportUser";

  // >>>>>>>> >>>>>>>>>>>>>> get block user api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const blockListApi = "${baseUrl}api/client/block/getBlockedUsers";

  // >>>>>>>> >>>>>>>>>>>>>> ad report api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const adReportApi = "${baseUrl}api/client/report/reportAd";

  // >>>>>>>> >>>>>>>>>>>>>> give review api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const giveReviewApi = "${baseUrl}api/client/review/giveReview";

  // >>>>>>>> >>>>>>>>>>>>>> get review api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const getReviewApi = "${baseUrl}api/client/review/retrieveReview";

  // >>>>>>>> >>>>>>>>>>>>>> notification switch api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const notificationApi =
      "${baseUrl}api/client/user/manageUserPermission";

  // >>>>>>>> >>>>>>>>>>>>>> fetch Live auction ad listing api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const fetchAuctionAdListing =
      "${baseUrl}api/client/adListing/fetchAuctionAdListings?";

  // >>>>>>>> >>>>>>>>>>>>>> Place Bid api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const placeBidApi = "${baseUrl}api/client/auctionBid/placeManualBid";

  // >>>>>>>> >>>>>>>>>>>>>> seller product info using upload video screen  >>>>>>>>>>> >>>>>>>>>>>> //
  static const sellerProductInfo =
      "${baseUrl}api/client/adListing/getSellerProductsBasicInfo";

  // >>>>>>>> >>>>>>>>>>>>>> favorite ads api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const fetchMostLikedAdsApi =
      "${baseUrl}api/client/adListing/fetchMostLikedAdListings";

  // >>>>>>>> >>>>>>>>>>>>>> get specific product like api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const specificProductLikeApi =
      "${baseUrl}api/client/adLike/getLikesForAd";

  // >>>>>>>> >>>>>>>>>>>>>> get specific product view api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const specificProductViewApi =
      "${baseUrl}api/client/adView/getAdViews";

  // >>>>>>>> >>>>>>>>>>>>>> get specific product view api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const updateVideoApi = "${baseUrl}api/client/adVideo/updateAdVideo";

  // >>>>>>>> >>>>>>>>>>>>>> user follow unfollow api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const userFollowUnFollowApi =
      "${baseUrl}api/client/follow/toggleFollowStatus";

  // >>>>>>>> >>>>>>>>>>>>>> get Social Connections api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const getSocialConnectionsApi =
      "${baseUrl}api/client/follow/getSocialConnections";

  // >>>>>>>> >>>>>>>>>>>>>> get user ads api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const getSellerProduct =
      "${baseUrl}api/client/adListing/getAdListingsOfSeller";

  // >>>>>>>> >>>>>>>>>>>>>> get Social Connections api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const recordVideoView =
      "${baseUrl}api/client/videoView/recordVideoView";

  // >>>>>>>> >>>>>>>>>>>>>> user liked ad list api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const userLikedAdListApi =
      "${baseUrl}api/client/adLike/fetchLikedAdListingRecords?";

  // >>>>>>>> >>>>>>>>>>>>>> notification list api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const notificationListApi =
      "${baseUrl}api/client/notification/getMyNotifications";

  // >>>>>>>> >>>>>>>>>>>>>> reel Report Api   >>>>>>>>>>> >>>>>>>>>>>> //
  static const reelReportApi = "${baseUrl}api/client/report/reportAdVideo";

  // >>>>>>>> >>>>>>>>>>>>>> product detail Api   >>>>>>>>>>> >>>>>>>>>>>> //
  static const productDetailApi =
      "${baseUrl}api/client/adListing/fetchAdDetailsById";

  // >>>>>>>> >>>>>>>>>>>>>> promote Ads Api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const promoteAdsApi = "${baseUrl}api/client/adListing/promoteAds";

  // >>>>>>>> >>>>>>>>>>>>>> forget password Api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const forgotPassword =
      "${baseUrl}api/client/user/initiatePasswordReset";

  // >>>>>>>> >>>>>>>>>>>>>> delete notification Ads Api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const clearMyNotifications =
      "${baseUrl}api/client/notification/clearMyNotifications";

  // >>>>>>>> >>>>>>>>>>>>>> Wallet Api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const walletBalance =
      "${baseUrl}api/client/wallet/getBalance";
  static const walletTransactions =
      "${baseUrl}api/client/wallet/getTransactions?";

  // >>>>>>>> >>>>>>>>>>>>>> Withdraw Requests Api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const withdrawList =
      "${baseUrl}api/client/withdraw/getWithdrawRequests?";
  static const submitWithdraw =
      "${baseUrl}api/client/withdraw/submitWithdrawRequest";

  // >>>>>>>> >>>>>>>>>>>>>> Support Tickets Api  >>>>>>>>>>> >>>>>>>>>>>> //
  static const supportTickets =
      "${baseUrl}api/client/support/getTickets?";
  static const createSupportTicket =
      "${baseUrl}api/client/support/createTicket";
  static const supportTicketDetail =
      "${baseUrl}api/client/support/getTicket/";
  static const replyTicket =
      "${baseUrl}api/client/support/replyTicket/";
}
