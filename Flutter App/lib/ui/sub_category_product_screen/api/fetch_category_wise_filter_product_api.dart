// // import 'dart:convert';
// // import 'dart:developer';
// // import 'package:http/http.dart' as http;
// // import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
// // import 'package:listify/utils/api.dart';
// // import 'package:listify/utils/api_params.dart';
// // import 'package:listify/utils/database.dart';
// // import 'package:listify/utils/firebse_access_token.dart';
// // import 'package:listify/utils/utils.dart';
// //
// // class FetchCategoryWiseFilterProductApi {
// //   static Future<AllAdsResponseModel?> callApi({
// //     required String start,
// //     required String limit,
// //     String? userId,
// //     String? categoryId,
// //     String? country,
// //     String? state,
// //     String? city,
// //     String? minPrice,
// //     String? maxPrice,
// //     String? latitude,
// //     String? longitude,
// //     String? postedSince,
// //     String? search,
// //     String? sort,
// //     List<Map<String, dynamic>>? attributes,
// //   }) async {
// //     Utils.showLog("Fetch CategoryWiseFilterProductApi GET API Calling...");
// //
// //     final token = await FirebaseAccessToken.onGet();
// //
// //     // build query params
// //     final queryParams = {
// //       "start": start,
// //       "limit": limit,
// //       "userId": userId,
// //       "categoryId": categoryId,
// //       "country": country,
// //       "state": state,
// //       "city": city,
// //       "minPrice": minPrice,
// //       "maxPrice": maxPrice,
// //       "latitude": latitude,
// //       "longitude": longitude,
// //       "postedSince": postedSince,
// //       "search": search,
// //       "sort": sort,
// //       "attributes": jsonEncode(attributes), // 👈 encode attributes as JSON string
// //     };
// //
// //     Utils.showLog("filter quary parameters ${queryParams}");
// //
// //     final uri = Uri.parse(Api.fetchCategoryWiseAdListings).replace(queryParameters: queryParams);
// //
// //     final headers = {
// //       ApiParams.key: Api.secretKey,
// //       ApiParams.contentType: "application/json",
// //       ApiParams.authToken: "Bearer $token",
// //       ApiParams.authUid: "${Database.getUserProfileResponseModel?.user?.firebaseUid}",
// //     };
// //
// //     Utils.showLog("All Ads filter api URI :: $uri");
// //     Utils.showLog("All Ads Headers :: $headers");
// //
// //     try {
// //       final response = await http.get(uri, headers: headers);
// //
// //       log('All Ads filter STATUS CODE :: ${response.statusCode} \n filter RESPONSE :: ${response.body}');
// //
// //       if (response.statusCode == 200) {
// //         final jsonResponse = json.decode(response.body);
// //         return AllAdsResponseModel.fromJson(jsonResponse);
// //       } else {
// //         throw Exception('All Ads Status code is not 200');
// //       }
// //     } catch (e) {
// //       log("All Ads Error :: $e");
// //     }
// //     return null;
// //   }
// // }
// import 'dart:convert';
// import 'dart:developer';
// import 'package:http/http.dart' as http;
// import 'package:listify/ui/my_ads_screen/model/all_ads_response_model.dart';
// import 'package:listify/utils/api.dart';
// import 'package:listify/utils/api_params.dart';
// import 'package:listify/utils/database.dart';
// import 'package:listify/utils/firebse_access_token.dart';
// import 'package:listify/utils/utils.dart';
//
// class FetchCategoryWiseFilterProductApi {
//   static Future<AllAdsResponseModel?> callApi({
//     required String start,
//     required String limit,
//     String? userId,
//     String? categoryId,
//     String? country,
//     String? state,
//     String? city,
//     String? minPrice,
//     String? maxPrice,
//     String? latitude,
//     String? longitude,
//     String? postedSince,
//     String? search,
//     String? sort,
//     List<Map<String, dynamic>>? attributes,
//     String? rangeInKm, // optional if backend supports it
//   }) async {
//     Utils.showLog("FetchCategoryWiseFilterProductApi GET-with-BODY Calling...");
//
//     final token = await FirebaseAccessToken.onGet();
//
//     // ✅ Build JSON body (types aligned if your backend expects numbers)
//     final Map<String, dynamic> body = {
//       "start": int.tryParse(start) ?? 1,
//       "limit": int.tryParse(limit) ?? 10,
//       "userId": userId,
//       "categoryId": categoryId,
//       "country": country,
//       "state": state,
//       "city": city,
//       "minPrice": minPrice != null ? int.tryParse(minPrice) : null,
//       "maxPrice": maxPrice != null ? int.tryParse(maxPrice) : null,
//       "postedSince": postedSince, // e.g. "2025-08-01" or "all_time"
//       "search": search,
//       "latitude": latitude != null ? double.tryParse(latitude) : null,
//       "longitude": longitude != null ? double.tryParse(longitude) : null,
//       "rangeInKm": rangeInKm != null ? int.tryParse(rangeInKm) : null,
//       "sort": sort, // e.g. "price_desc" | "price_asc" | "new" ...
//       "attributes": attributes ?? <Map<String, dynamic>>[],
//     };
//
//     // Remove nulls to keep payload clean
//     body.removeWhere((k, v) => v == null);
//
//     final uri = Uri.parse(Api.fetchCategoryWiseAdListings); // e.g. http://.../fetchCategoryWiseAdListings
//
//     final headers = <String, String>{
//       ApiParams.key: Api.secretKey, // 'key': '...'
//       ApiParams.contentType: "application/json", // 'Content-Type': 'application/json'
//       ApiParams.authToken: "Bearer $token", // 'Authorization': 'Bearer ...'
//       ApiParams.authUid: "${Database.getUserProfileResponseModel?.user?.firebaseUid}",
//     };
//
//     Utils.showLog("🌍 GET URI :: $uri");
//     Utils.showLog("📄 Headers :: $headers");
//     Utils.showLog("📦 Body :: ${jsonEncode(body)}");
//
//     try {
//       // ⚠️ Use http.Request to send a GET WITH BODY
//       final request = http.Request('GET', uri)
//         ..headers.addAll(headers)
