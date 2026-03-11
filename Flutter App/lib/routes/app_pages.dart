import 'package:get/get.dart';
import 'package:listify/routes/app_routes.dart';
import 'package:listify/ui/about_us_screen/view/about_us_screen.dart';
import 'package:listify/ui/add_listing_screen/binding/add_listing_screen_binding.dart';
import 'package:listify/ui/add_listing_screen/view/add_listing_screen.dart';
import 'package:listify/ui/add_product_screen/binding/add_product_screen_binding.dart';
import 'package:listify/ui/add_product_screen/view/add_product_screen_view.dart';
import 'package:listify/ui/block_screen/binding/block_screen_binding.dart';
import 'package:listify/ui/block_screen/view/block_screen_view.dart';
import 'package:listify/ui/blog_screen/binding/blog_screen_binding.dart';
import 'package:listify/ui/blog_screen/view/blog_screen.dart';
import 'package:listify/ui/bottom_bar/binding/bottom_bar_binding.dart';
import 'package:listify/ui/bottom_bar/view/bottom_bar_screen.dart';
import 'package:listify/ui/categories_screen/binding/categories_screen_binding.dart';
import 'package:listify/ui/categories_screen/view/categories_screen_view.dart';
import 'package:listify/ui/chat_detail_screen/binding/chat_detail_binding.dart';
import 'package:listify/ui/chat_detail_screen/view/chat_detail_screen_view.dart';
import 'package:listify/ui/confirm_location/binding/confirm_location_binding.dart';
import 'package:listify/ui/confirm_location/view/confirm_location_screen.dart';
import 'package:listify/ui/contact_us_screen/binding/contact_us_screen_binding.dart';
import 'package:listify/ui/contact_us_screen/view/contact_us_screen.dart';
import 'package:listify/ui/edit_product_screen/binding/edit_product_detail_binding.dart';
import 'package:listify/ui/edit_product_screen/view/edit_product_view.dart';
import 'package:listify/ui/edit_profile_screen/binding/edit_profile_binding.dart';
import 'package:listify/ui/edit_profile_screen/view/edit_profile_view.dart';
import 'package:listify/ui/faq_screen/binding/faq_screen_binding.dart';
import 'package:listify/ui/faq_screen/view/faq_screen.dart';
import 'package:listify/ui/fashion_blog_screen/binding/fashion_blog_screen_binding.dart';
import 'package:listify/ui/fashion_blog_screen/view/fashion_blog_screen.dart';
import 'package:listify/ui/favourrite_screen/binding/favourite_screen_binding.dart';
import 'package:listify/ui/favourrite_screen/view/favourite_screen.dart';
import 'package:listify/ui/featured_ads_screen/binding/featured_ads_screen_binding.dart';
import 'package:listify/ui/featured_ads_screen/binding/featured_ads_show_screen_binding.dart';
import 'package:listify/ui/featured_ads_screen/view/featured_ads_screen.dart';
import 'package:listify/ui/featured_ads_screen/view/featured_ads_show_screen.dart';
import 'package:listify/ui/fill_profile_screen/binding/fill_profile_screen_binding.dart';
import 'package:listify/ui/fill_profile_screen/view/fill_profile_screen_view.dart';
import 'package:listify/ui/forgot_password_screen/binding/forgot_password_binding.dart';
import 'package:listify/ui/forgot_password_screen/view/forgot_password_screen.dart';
import 'package:listify/ui/home_screen/binding/home_screen_binding.dart';
import 'package:listify/ui/home_screen/view/home_screen.dart';
import 'package:listify/ui/home_search_product_screen/binding/home_search_product_binding.dart';
import 'package:listify/ui/home_search_product_screen/view/home_screen_product_screen_view.dart';
import 'package:listify/ui/language_screen/binding/language_screen_binding.dart';
import 'package:listify/ui/language_screen/view/language_screen_view.dart';
import 'package:listify/ui/live_auction_view_all_screen/binding/live_auction_screen_binding.dart';
import 'package:listify/ui/live_auction_view_all_screen/view/live_auction_view_all_screen.dart';
import 'package:listify/ui/location_screen/binding/location_screen_binding.dart';
import 'package:listify/ui/location_screen/view/location_screen.dart';
import 'package:listify/ui/login_screen/binding/login_screen_binding.dart';
import 'package:listify/ui/login_screen/view/login_screen.dart';
import 'package:listify/ui/message_screen/binding/message_screen_binding.dart';
import 'package:listify/ui/message_screen/view/message_screen_view.dart';
import 'package:listify/ui/mobile_number_screen/binding/mobile_number_binding.dart';
import 'package:listify/ui/mobile_number_screen/view/mobile_number_screen.dart';
import 'package:listify/ui/most_liked_view_all/binding/most_liked_view_all_binding.dart';
import 'package:listify/ui/most_liked_view_all/view/most_liked_view_all_screen.dart';
import 'package:listify/ui/my_ads_screen/view/my_ads_screen_view.dart';
import 'package:listify/ui/my_videos_screen/binding/my_videos_screen_binding.dart';
import 'package:listify/ui/my_videos_screen/view/my_videos_screen.dart';
import 'package:listify/ui/near_by_listing_screen/binding/near_by_listing_screen_binding.dart';
import 'package:listify/ui/near_by_listing_screen/view/near_by_listing_screen.dart';
import 'package:listify/ui/notification_screen/binding/notification_screen_binding.dart';
import 'package:listify/ui/notification_screen/view/notification_screen_view.dart';
import 'package:listify/ui/on_boarding_screen/binding/on_boarding_screen_binding.dart';
import 'package:listify/ui/on_boarding_screen/view/on_boarding_screen_view.dart';
import 'package:listify/ui/popular_poduct_screen/binding/popular_product_screen_binding.dart';
import 'package:listify/ui/popular_poduct_screen/view/popular_product_screen.dart';
import 'package:listify/ui/product_detail_screen/binding/product_detail_screen_binding.dart';
import 'package:listify/ui/product_detail_screen/binding/specific_product_like_view_binding.dart';
import 'package:listify/ui/product_detail_screen/binding/specific_product_view_binding.dart';
import 'package:listify/ui/product_detail_screen/controller/product_detail_screen_controller.dart';
import 'package:listify/ui/product_detail_screen/view/product_detail_screen_view.dart';
import 'package:listify/ui/product_detail_screen/view/specif_ad_like_show_screen.dart';
import 'package:listify/ui/product_detail_screen/view/specif_ad_view_show_screen.dart';
import 'package:listify/ui/product_pricing_screen/binding/product_pricing_screen_binding.dart';
import 'package:listify/ui/product_pricing_screen/view/product_pricing_screen.dart';
import 'package:listify/ui/profile_screen_view/binding/profile_screen_binding.dart';
import 'package:listify/ui/profile_screen_view/view/profile_screen_view.dart';
import 'package:listify/ui/registration_screen/binding/registration_binding.dart';
import 'package:listify/ui/registration_screen/view/registration_screen.dart';
import 'package:listify/ui/review_screen/binding/review_screen_binding.dart';
import 'package:listify/ui/review_screen/view/review_screen_view.dart';
import 'package:listify/ui/select_city_screen/binding/select_city_screen_binding.dart';
import 'package:listify/ui/select_city_screen/view/select_city_screen.dart';
import 'package:listify/ui/select_state_screen/binding/select_state_screen_binding.dart';
import 'package:listify/ui/select_state_screen/view/select_state_screen.dart';
import 'package:listify/ui/seller_detail_product_all_view/binding/seller_detail_product_all_view_binding.dart';
import 'package:listify/ui/seller_detail_product_all_view/view/seller_detail_product_all_view.dart';
import 'package:listify/ui/seller_detail_screen/binding/seller_detail_screen_binding.dart';
import 'package:listify/ui/seller_detail_screen/view/seller_detail_screen_view.dart';
import 'package:listify/ui/splash_screen/binding/splash_screen_binding.dart';
import 'package:listify/ui/splash_screen/view/splash_screen_view.dart';
import 'package:listify/ui/start_user_verification_screen/binding/start_user_verification_binding.dart';
import 'package:listify/ui/start_user_verification_screen/view/start_user_verification_view.dart';
import 'package:listify/ui/sub_categories_screen/binding/sub_categories_screen_binding.dart';
import 'package:listify/ui/sub_categories_screen/view/sub_categories_screen.dart';
import 'package:listify/ui/sub_category_product_screen/binding/sub_category_product_screen_binding.dart';
import 'package:listify/ui/sub_category_product_screen/view/product_filter_screen.dart';
import 'package:listify/ui/sub_category_product_screen/view/sub_category_product_screen.dart';
import 'package:listify/ui/subscription%20_plan_screen/binding/subscription_plan_screen_binding.dart';
import 'package:listify/ui/subscription%20_plan_screen/view/subscription_plan_screen.dart';
import 'package:listify/ui/transaction_history/binding/transaction_history_screen_binding.dart';
import 'package:listify/ui/transaction_history/view/transaction_history_screen_view.dart';
import 'package:listify/ui/upload_image_screen/binding/upload_image_screen_binding.dart';
import 'package:listify/ui/upload_image_screen/view/upload_image_screen_view.dart';
import 'package:listify/ui/upload_video_screen/binding/upload_video_screen_binding.dart';
import 'package:listify/ui/upload_video_screen/view/upload_video_detail_screen.dart';
import 'package:listify/ui/upload_video_screen/view/upload_video_screen.dart';
import 'package:listify/ui/user_verification_screen/binding/user_verification_screen_binding.dart';
import 'package:listify/ui/user_verification_screen/view/user_verification_screen_view.dart';
import 'package:listify/ui/verify_otp_screen/binding/verify_otp_binding.dart';
import 'package:listify/ui/verify_otp_screen/view/verify_otp_screen.dart';
import 'package:listify/ui/videos_screen/binding/videos_screen_binding.dart';
import 'package:listify/ui/videos_screen/view/videos_screen_view.dart';
import 'package:listify/ui/wallet_screen/binding/wallet_screen_binding.dart';
import 'package:listify/ui/wallet_screen/view/wallet_screen_view.dart';
import 'package:listify/ui/withdraw_screen/binding/withdraw_screen_binding.dart';
import 'package:listify/ui/withdraw_screen/view/withdraw_screen_view.dart';
import 'package:listify/ui/support_ticket_screen/binding/support_ticket_binding.dart';
import 'package:listify/ui/support_ticket_screen/view/support_ticket_screen_view.dart';
import 'package:listify/ui/support_ticket_screen/view/support_ticket_detail_view.dart';
import 'package:listify/ui/escrow_screen/binding/escrow_screen_binding.dart';
import 'package:listify/ui/escrow_screen/view/escrow_screen_view.dart';
import 'package:listify/ui/escrow_screen/view/escrow_detail_screen_view.dart';
import 'package:listify/ui/banner_ad_screen/binding/banner_ad_screen_binding.dart';
import 'package:listify/ui/banner_ad_screen/view/banner_ad_screen_view.dart';
import 'package:listify/ui/banner_ad_screen/view/banner_ad_submit_view.dart';
import 'package:listify/ui/change_password_screen/binding/change_password_binding.dart';
import 'package:listify/ui/change_password_screen/view/change_password_view.dart';

class AppPages {
  static List<GetPage> list = [
    GetPage(
      name: AppRoutes.homeScreen,
      page: () => HomeScreen(),
      binding: HomeScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.bottomBar,
      page: () => BottomBarScreen(),
      binding: BottomBarBinding(),
    ),
    GetPage(
      name: AppRoutes.myAdsScreen,
      page: () => MyAdsScreenView(),
      // binding: BottomBarBinding(),
    ),
    GetPage(
      name: AppRoutes.videosScreen,
      page: () => VideosScreenView(),
      binding: VideosScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.messageScreen,
      page: () => MessageScreenView(),
      binding: MessageScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.addProductScreen,
      page: () => AddProductScreenView(),
      binding: AddProductScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.liveAuctionScreen,
      page: () => LiveAuctionViewAllScreen(),
      binding: LiveAuctionScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.categoriesScreen,
      page: () => CategoriesScreenView(),
      binding: CategoriesScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.subCategoriesScreen,
      page: () => SubCategoriesScreen(),
      binding: SubCategoriesScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.productDetailScreen,
      page: () => ProductDetailScreenView(),
      binding: ProductDetailScreenBinding(),
    ),


    // GetPage(
    //   name: AppRoutes.productDetailScreen,
    //   page: () => const ProductDetailScreenView(),
    //   binding: BindingsBuilder(() {
    //     Get.put(ProductDetailScreenController());
    //   }),
    // ),

    GetPage(
      name: AppRoutes.subCategoryProductScreen,
      page: () => SubCategoryProductScreen(),
      binding: SubCategoryProductScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.productFilterScreen,
      page: () => ProductFilterScreen(),
      binding: SubCategoryProductScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.editProductView,
      page: () => EditProductView(),
      binding: EditProductDetailBinding(),
    ),
    GetPage(
      name: AppRoutes.uploadImageScreenView,
      page: () => UploadImageScreenView(),
      binding: UploadImageScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.locationScreen,
      page: () => LocationScreen(),
      binding: LocationScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.nearByListingScreen,
      page: () => NearByListingScreen(),
      binding: NearByListingScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.selectStateScreen,
      page: () => SelectStateScreen(),
      binding: SelectStateScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.selectCityScreen,
      page: () => SelectCityScreen(),
      binding: SelectCityScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.confirmLocationScreen,
      page: () => ConfirmLocationScreen(),
      binding: ConfirmLocationBinding(),
    ),
    GetPage(
      name: AppRoutes.productPricingScreen,
      page: () => ProductPricingScreen(),
      binding: ProductPricingScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.favoriteScreen,
      page: () => FavoriteScreen(),
      binding: FavoriteScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.addListingScreen,
      page: () => AddListingScreen(),
      binding: AddListingScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.blogScreen,
      page: () => BlogScreen(),
      binding: BlogScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.fashionBlogScreen,
      page: () => FashionBlogScreen(),
      binding: FashionBlogScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.faqScreen,
      page: () => FaqScreen(),
      binding: FaqScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.contactUsScreen,
      page: () => ContactUsScreen(),
      binding: ContactUsScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.videoScreenView,
      page: () => VideosScreenView(),
      binding: VideosScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.profileScreenView,
      page: () => ProfileScreenView(),
      binding: ProfileScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.editProfileView,
      page: () => EditProfileView(),
      binding: EditProfileBinding(),
    ),
    GetPage(
      name: AppRoutes.aboutUsScreen,
      page: () => AboutUsScreen(),
      // binding: About(),
    ),
    GetPage(
      name: AppRoutes.myVideosScreen,
      page: () => MyVideosScreen(),
      binding: MyVideosScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.uploadVideoScreen,
      page: () => UploadVideoScreen(),
      binding: UploadVideoScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.uploadVideoDetailScreen,
      page: () => UploadVideoDetailScreen(),
      binding: UploadVideoScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.featuredAdsScreen,
      page: () => FeaturedAdsScreen(),
      binding: FeaturedAdsScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.featuredAdsShowScreen,
      page: () => FeaturedAdsShowScreen(),
      binding: FeaturedAdsShowScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.subscriptionPlanScreen,
      page: () => SubscriptionPlanScreen(),
      binding: SubscriptionPlanScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.languageScreenView,
      page: () => LanguageScreenView(),
      binding: LanguageScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.transactionHistoryScreenView,
      page: () => TransactionHistoryScreenView(),
      binding: TransactionHistoryScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.userVerificationView,
      page: () => StartUserVerificationView(),
      binding: StartUserVerificationBinding(),
    ),
    GetPage(
      name: AppRoutes.userVerificationScreenView,
      page: () => UserVerificationScreenView(),
      binding: UserVerificationScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.notificationScreenView,
      page: () => NotificationScreenView(),
      binding: NotificationScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.chatDetailScreenView,
      page: () => ChatDetailScreenView(),
      binding: ChatDetailBinding(),
    ),
    GetPage(
      name: AppRoutes.popularProductScreen,
      page: () => PopularProductScreen(),
      binding: PopularProductScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.loginScreen,
      page: () => LoginScreen(),
      binding: LoginScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.forgotPasswordScreen,
      page: () => ForgotPasswordScreen(),
      binding: ForgotPasswordBinding(),
    ),
    GetPage(
      name: AppRoutes.register,
      page: () => const RegistrationScreen(),
      binding: RegistrationBinding(),
    ),
    GetPage(
      name: AppRoutes.mobileLogIn,
      page: () => const MobileNumberScreen(),
      binding: MobileNumberBinding(),
    ),
    GetPage(
      name: AppRoutes.verifyOtp,
      page: () => const VerifyOtpScreen(),
      binding: VerifyOtpBinding(),
    ),
    GetPage(
      name: AppRoutes.fillProfileScreen,
      page: () => FillProfileView(),
      binding: FillProfileScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.splashScreenView,
      page: () => SplashScreenView(),
      binding: SplashScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.blockScreenView,
      page: () => BlockScreenView(),
      binding: BlockScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.reviewScreenView,
      page: () => ReviewScreenView(),
      binding: ReviewScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.sellerDetailScreenView,
      page: () => SellerDetailScreenView(),
      binding: SellerDetailScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.sellerDetailProductAllView,
      page: () => SellerDetailProductAllView(),
      binding: SellerDetailProductAllViewBinding(),
    ),
    GetPage(
      name: AppRoutes.onBoardingScreenView,
      page: () => OnBoardingScreenView(),
      binding: OnBoardingScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.specifAdLikeShowScreen,
      page: () => SpecifAdLikeShowScreen(),
      binding: SpecificProductLikeViewBinding(),
    ),
    GetPage(
      name: AppRoutes.specifAdViewShowScreen,
      page: () => SpecifAdViewShowScreen(),
      binding: SpecificProductViewBinding(),
    ),
    GetPage(
      name: AppRoutes.mostLikedViewAllScreen,
      page: () => MostLikedViewAllScreen(),
      binding: MostLikedViewAllBinding(),
    ),
    GetPage(
      name: AppRoutes.homeScreenProductScreenView,
      page: () => HomeScreenProductScreenView(),
      binding: HomeSearchProductBinding(),
    ),
    GetPage(
      name: AppRoutes.walletScreen,
      page: () => WalletScreenView(),
      binding: WalletScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.withdrawScreen,
      page: () => WithdrawScreenView(),
      binding: WithdrawScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.supportTicketScreen,
      page: () => SupportTicketScreenView(),
      binding: SupportTicketBinding(),
    ),
    GetPage(
      name: AppRoutes.supportTicketDetailScreen,
      page: () => SupportTicketDetailView(),
      binding: SupportTicketBinding(),
    ),
    GetPage(
      name: AppRoutes.escrowOrdersScreen,
      page: () => EscrowScreenView(),
      binding: EscrowScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.escrowDetailScreen,
      page: () => EscrowDetailScreenView(),
      binding: EscrowScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.bannerAdScreen,
      page: () => BannerAdScreenView(),
      binding: BannerAdScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.bannerAdSubmitScreen,
      page: () => BannerAdSubmitView(),
      binding: BannerAdScreenBinding(),
    ),
    GetPage(
      name: AppRoutes.changePasswordScreen,
      page: () => const ChangePasswordView(),
      binding: ChangePasswordBinding(),
    ),
  ];
}
