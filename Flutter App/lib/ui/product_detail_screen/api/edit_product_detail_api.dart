import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/product_pricing_screen/model/update_product_detail_response_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';

///old
// class UpdateListingApi {
//   static Future<UpdateProductDetailResponseModel?> callApi({
//     required String uid,
//     required String adId,
//     required String primaryImagePath,
//     required List<String> galleryImagePaths,
//     required List<int> galleryIndexes,
//     required String title,
//     required String subTitle,
//     required String description,
//     required String contactNumber,
//     required String location,
//     required String price,
//     required String minimumOffer,
//     required String availableUnits,
//     required bool isReservePriceEnabled,
//     required bool isOfferAllowed,
//     required bool isAuctionEnabled,
//     required List<Map<String, dynamic>> attributes,
//     required String reservePriceAmount,
//     required String auctionStartingPrice,
//     required int auctionDurationDays,
//     required String scheduledPublishDate,
//   }) async {
//     Utils.showLog("Update Ad Listing API Calling...");
//
//     final token = await FirebaseAccessToken.onGet();
//
//     try {
//       var request = http.MultipartRequest(
//         'PATCH',
//         Uri.parse(Api.updateListingApi),
//       );
//
//       var headers = {
//         ApiParams.key: Api.secretKey,
//         ApiParams.authToken: 'Bearer $token',
//         ApiParams.authUid: uid,
//         ApiParams.contentType: 'application/json',
//       };
//
//       request.headers.addAll(headers);
//
//       request.fields.addAll({
//         ApiParams.adId: adId,
//         ApiParams.title: title,
//         ApiParams.subTitle: subTitle,
//         ApiParams.description: description,
//         ApiParams.contactNumber: contactNumber,
//         ApiParams.location: location,
//         ApiParams.price: price,
//         ApiParams.isOfferAllowed: isOfferAllowed.toString(),
//         ApiParams.minimumOffer: minimumOffer,
//         ApiParams.isAuctionEnabled: isAuctionEnabled.toString(),
//         ApiParams.isReservePriceEnabled: isReservePriceEnabled.toString(),
//         ApiParams.availableUnits: availableUnits,
//         ApiParams.attributes: jsonEncode(attributes),
//         ApiParams.galleryIndexes:  jsonEncode(galleryIndexes),
//         ApiParams.reservePriceAmount:  reservePriceAmount.toString(),
//         ApiParams.auctionStartingPrice:  auctionStartingPrice.toString(),
//         ApiParams.auctionDurationDays:  auctionDurationDays.toString(),
//         ApiParams.scheduledPublishDate: scheduledPublishDate,
//       });
//
//       // // Primary Image
//       // if (primaryImagePath.isNotEmpty) {
//       //   request.files.add(await http.MultipartFile.fromPath('primaryImage', primaryImagePath));
//       // }
//       //
//       // // Gallery Images
//       // for (String path in galleryImagePaths) {
//       //   request.files.add(await http.MultipartFile.fromPath('galleryImages', path));
//       // }
//
//       // Primary Image
//       if (primaryImagePath.isNotEmpty) {
//         if (primaryImagePath.contains('/storage') ||
//             primaryImagePath.contains('data/user')) {
//           // Local file → upload karo
//           request.files.add(await http.MultipartFile.fromPath(
//               'primaryImage', primaryImagePath));
//         } else {
//           // Already server ma che → field ma moklo
//           request.fields['primaryImage'] = primaryImagePath;
//         }
//       }
//
//       // Gallery Images
//       for (String path in galleryImagePaths) {
//         if (path.contains('/storage') || path.contains('data/user')) {
//           // Local image → upload karo
//           request.files
//               .add(await http.MultipartFile.fromPath('galleryImages', path));
//           log("📂 Local Gallery Image Added => $path");
//         } else {
//           // Server image → JSON/field ma add karo
//           request.fields['galleryImages[]'] = path;
//           log("🌐 Server Gallery Image Added => $path");
//         }
//       }
//
//       Utils.showLog("Update Ad Listing Request Fields => ${request.fields}");
//       Utils.showLog(
//           "Update Ad Listing Request Fields primaryImagePath=> $primaryImagePath");
//       Utils.showLog(
//           "Update Ad Listing Request Fields galleryImagePaths=> $galleryImagePaths");
//       Utils.showLog("Update Ad Listing Files => ${request.files.length}");
//
//       final response = await request.send();
//       final responseBody = await response.stream.bytesToString();
//
//       log("Update Ad Listing Response Code => ${response.statusCode}");
//       log("Update Ad Listing Response => $responseBody");
//
//       if (response.statusCode == 200) {
//         final jsonResponse = jsonDecode(responseBody);
//         return UpdateProductDetailResponseModel.fromJson(jsonResponse);
//       } else {
//         return null;
//       }
//     } catch (e) {
//       Utils.showLog("Update Ad Listing Error => $e");
//       return null;
//     }
//   }
// }


///6 last all feild in api

// class UpdateListingApi {
//   static Future<UpdateProductDetailResponseModel?> callApi({
//     required String uid,
//     required String adId,
//     required Map<String, dynamic> changedFields,          // <-- only diffs
//     required List<int> removedGalleryIndexes,             // <-- for deletion by index
//   }) async {
//     Utils.showLog("Update Ad Listing API Calling...");
//     Utils.showLog("🔥 Diffs => ${jsonEncode(changedFields)}");
//
//     final token = await FirebaseAccessToken.onGet();
//
//     try {
//       // ❗Do NOT set content-type for multipart (it auto-adds boundary)
//       final request = http.MultipartRequest(
//         'PATCH',
//         Uri.parse(Api.updateListingApi),
//       );
//
//       final headers = <String, String>{
//         ApiParams.key: Api.secretKey,
//         ApiParams.authToken: 'Bearer $token', // or 'Authorization'
//         ApiParams.authUid: uid,
//       };
//       request.headers.addAll(headers);
//
//       // Always required
//       request.fields[ApiParams.adId] = adId;
//
//       // Helper to put simple string fields
//       void putField(String apiKey, dynamic value) {
//         if (value == null) return;
//         request.fields[apiKey] = value.toString();
//       }
//
//       // ---- Map UI keys -> API keys (adjust ApiParams.* to your constants) ----
//       if (changedFields.containsKey('title')) putField(ApiParams.title, changedFields['title']);
//       if (changedFields.containsKey('subTitle')) putField(ApiParams.subTitle, changedFields['subTitle']);
//       if (changedFields.containsKey('description')) putField(ApiParams.description, changedFields['description']);
//       if (changedFields.containsKey('location')) {
//         // location is JSON string already
//         putField(ApiParams.location, changedFields['location']);
//       }
//       if (changedFields.containsKey('price')) putField(ApiParams.price, changedFields['price']);
//       if (changedFields.containsKey('minimumOffer')) putField(ApiParams.minimumOffer, changedFields['minimumOffer']);
//       if (changedFields.containsKey('availableUnits')) putField(ApiParams.availableUnits, changedFields['availableUnits']);
//
//       if (changedFields.containsKey('isOfferAllowed')) {
//         putField(ApiParams.isOfferAllowed, (changedFields['isOfferAllowed'] == true).toString());
//       }
//       if (changedFields.containsKey('isAuctionEnabled')) {
//         putField(ApiParams.isAuctionEnabled, (changedFields['isAuctionEnabled'] == true).toString());
//       }
//       if (changedFields.containsKey('isReservePriceEnabled')) {
//         putField(ApiParams.isReservePriceEnabled, (changedFields['isReservePriceEnabled'] == true).toString());
//       }
//
//       if (changedFields.containsKey('attributes')) {
//         // send proper JSON
//         request.fields[ApiParams.attributes] = jsonEncode(changedFields['attributes']);
//       }
//
//       // removed gallery indexes (deletions at backend)
//       if (removedGalleryIndexes.isNotEmpty) {
//         request.fields[ApiParams.galleryIndexes] = jsonEncode(removedGalleryIndexes);
//       }
//       if (changedFields.containsKey('galleryIndexes')) {
//         // in case diff builder added it here
//         final List<int> idxs = (changedFields['galleryIndexes'] as List?)?.map((e) => (e as num).toInt()).toList() ?? [];
//         if (idxs.isNotEmpty) {
//           request.fields[ApiParams.galleryIndexes] = jsonEncode(idxs);
//         }
//       }
//
//       // ---- Primary image (changed only) ----
//       if (changedFields.containsKey('primaryImage')) {
//         final String path = (changedFields['primaryImage'] ?? '').toString();
//         if (path.isNotEmpty) {
//           final isLocal = path.contains('/storage') || path.contains('data/user');
//           if (isLocal) {
//             request.files.add(await http.MultipartFile.fromPath('primaryImage', path));
//             log("📂 Primary local => $path");
//           } else {
//             // server URL keeps as field
//             request.fields['primaryImage'] = path;
//             log("🌐 Primary URL => $path");
//           }
//         }
//       }
//
//       // ---- Gallery (only if changed) ----
//       if (changedFields.containsKey('galleryImages')) {
//         final List<String> all = (changedFields['galleryImages'] as List?)?.map((e) => e.toString()).toList() ?? [];
//         final List<String> urls = [];
//         for (final p in all) {
//           final isLocal = p.contains('/storage') || p.contains('data/user');
//           if (isLocal) {
//             request.files.add(await http.MultipartFile.fromPath('galleryImages', p));
//             log("📂 Gallery local => $p");
//           } else {
//             urls.add(p);
//           }
//         }
//         if (urls.isNotEmpty) {
//           // IMPORTANT: use a single field with JSON array to avoid overwrites
//           // Adjust the key to your backend (e.g., 'existingGallery', 'galleryImagesUrls', etc.)
//           request.fields['galleryImagesUrls'] = jsonEncode(urls);
//         }
//       }
//
//       // (Optional) If backend *requires* contact number always
//       // putField(ApiParams.contactNumber, '9876543210');
//
//       Utils.showLog("Update Fields => ${request.fields}");
//       Utils.showLog("Update Files Count => ${request.files.length}");
//
//       final streamed = await request.send();
//       final status = streamed.statusCode;
//       final body = await streamed.stream.bytesToString();
//
//       log("Update Response Code => $status");
//       log("Update Raw => $body");
//
//       // ---- Robust parsing: handles double-encoded or message-in-data ----
//       if (status >= 200 && status < 300) {
//         dynamic decoded;
//         try {
//           decoded = jsonDecode(body);
//         } catch (_) {
//           throw StateError('Non-JSON response from server: $body');
//         }
//
//         // sometimes API returns string that itself is JSON
//         if (decoded is String) {
//           try {
//             decoded = jsonDecode(decoded);
//           } catch (_) {
//             decoded = {"status": true, "message": decoded};
//           }
//         }
//
//         if (decoded is Map<String, dynamic>) {
//           // tolerate data being string
//           final map = Map<String, dynamic>.from(decoded);
//           final rawData = map['data'];
//           if (rawData is String) {
//             // keep message friendly
//             map['message'] = (map['message'] ?? rawData).toString();
//             map['data'] = null;
//           }
//           return UpdateProductDetailResponseModel.fromJson(map);
//         }
//         throw StateError('Unexpected JSON shape: ${decoded.runtimeType}');
//       } else {
//         // try parse error body into model (status=false)
//         try {
//           final err = jsonDecode(body);
//           if (err is Map<String, dynamic>) {
//             return UpdateProductDetailResponseModel.fromJson(err);
//           }
//         } catch (_) {}
//         return null;
//       }
//     } catch (e, st) {
//       Utils.showLog("Update Ad Listing Error => $e");
//       log("STACK => $st");
//       return null;
//     }
//   }
// }


///7
// class UpdateListingApi {
//   static Future<UpdateProductDetailResponseModel?> callApi({
//     required String uid,
//     required String adId,
//     required Map<String, dynamic> changedFields,   // <-- ફક્ત diffs
//     required List<int> removedGalleryIndexes,      // <-- deletion by index (optional)
//   }) async {
//     Utils.showLog("Update Ad Listing API Calling...");
//     Utils.showLog("🔥 Diffs (incoming) => ${jsonEncode(changedFields)}");
//
//     final token = await FirebaseAccessToken.onGet();
//
//     try {
//       final request = http.MultipartRequest('PATCH', Uri.parse(Api.updateListingApi));
//
//       // ---- Tracking sets for debug ----
//       final Set<String> uiKeysSent = {};     // keys from changedFields that we used
//       final Set<String> apiKeysSent = {};    // actual multipart field keys
//       final List<String> fileParts = [];     // uploaded file field names
//
//       // ---- Headers ----
//       request.headers.addAll({
//         ApiParams.key: Api.secretKey,
//         ApiParams.authToken: 'Bearer $token',
//         ApiParams.authUid: uid,
//       });
//
//       // ---- Always required ----
//       request.fields[ApiParams.adId] = adId;
//       apiKeysSent.add(ApiParams.adId);
//
//       // Helper to put simple string fields
//       void putField(String apiKey, dynamic value, {String? uiKey}) {
//         if (value == null) return;
//         request.fields[apiKey] = value.toString();
//         apiKeysSent.add(apiKey);
//         if (uiKey != null) uiKeysSent.add(uiKey);
//       }
//
//       // ---- Map UI keys -> API keys (only if present in changedFields) ----
//       if (changedFields.containsKey('title')) {
//         putField(ApiParams.title, changedFields['title'], uiKey: 'title');
//       }
//       if (changedFields.containsKey('subTitle')) {
//         putField(ApiParams.subTitle, changedFields['subTitle'], uiKey: 'subTitle');
//       }
//       if (changedFields.containsKey('description')) {
//         putField(ApiParams.description, changedFields['description'], uiKey: 'description');
//       }
//       if (changedFields.containsKey('location')) {
//         // location should be JSON string already
//         putField(ApiParams.location, changedFields['location'], uiKey: 'location');
//       }
//
//       if (changedFields.containsKey('price')) {
//         putField(ApiParams.price, changedFields['price'], uiKey: 'price');
//       }
//       if (changedFields.containsKey('minimumOffer')) {
//         putField(ApiParams.minimumOffer, changedFields['minimumOffer'], uiKey: 'minimumOffer');
//       }
//       if (changedFields.containsKey('availableUnits')) {
//         putField(ApiParams.availableUnits, changedFields['availableUnits'], uiKey: 'availableUnits');
//       }
//
//       if (changedFields.containsKey('isOfferAllowed')) {
//         putField(ApiParams.isOfferAllowed, (changedFields['isOfferAllowed'] == true).toString(), uiKey: 'isOfferAllowed');
//       }
//       if (changedFields.containsKey('isAuctionEnabled')) {
//         putField(ApiParams.isAuctionEnabled, (changedFields['isAuctionEnabled'] == true).toString(), uiKey: 'isAuctionEnabled');
//       }
//       if (changedFields.containsKey('isReservePriceEnabled')) {
//         putField(ApiParams.isReservePriceEnabled, (changedFields['isReservePriceEnabled'] == true).toString(), uiKey: 'isReservePriceEnabled');
//       }
//
//       if (changedFields.containsKey('attributes')) {
//         request.fields[ApiParams.attributes] = jsonEncode(changedFields['attributes']);
//         apiKeysSent.add(ApiParams.attributes);
//         uiKeysSent.add('attributes');
//       }
//
//       // ---- removed gallery indexes ----
//       if (removedGalleryIndexes.isNotEmpty) {
//         request.fields[ApiParams.galleryIndexes] = jsonEncode(removedGalleryIndexes);
//         apiKeysSent.add(ApiParams.galleryIndexes);
//         // પ્રેક્ટિકલી આ UI key 'galleryIndexes' તરીકે ગણાય
//         uiKeysSent.add('galleryIndexes');
//       }
//       if (changedFields.containsKey('galleryIndexes')) {
//         final List<int> idxs = (changedFields['galleryIndexes'] as List?)?.map((e) => (e as num).toInt()).toList() ?? [];
//         if (idxs.isNotEmpty) {
//           request.fields[ApiParams.galleryIndexes] = jsonEncode(idxs);
//           apiKeysSent.add(ApiParams.galleryIndexes);
//           uiKeysSent.add('galleryIndexes');
//         }
//       }
//
//       // ---- Primary image ----
//       if (changedFields.containsKey('primaryImage')) {
//         final String path = (changedFields['primaryImage'] ?? '').toString();
//         if (path.isNotEmpty) {
//           final isLocal = path.contains('/storage') || path.contains('data/user');
//           if (isLocal) {
//             request.files.add(await http.MultipartFile.fromPath('primaryImage', path));
//             fileParts.add('primaryImage');
//             uiKeysSent.add('primaryImage');
//           } else {
//             request.fields['primaryImage'] = path;
//             apiKeysSent.add('primaryImage');
//             uiKeysSent.add('primaryImage');
//           }
//         }
//       }
//
//       // ---- Gallery (only if changed) ----
//       if (changedFields.containsKey('galleryImages')) {
//         final List<String> all = (changedFields['galleryImages'] as List?)?.map((e) => e.toString()).toList() ?? [];
//         final List<String> urls = [];
//         for (final p in all) {
//           final isLocal = p.contains('/storage') || p.contains('data/user');
//           if (isLocal) {
//             request.files.add(await http.MultipartFile.fromPath('galleryImages', p));
//             fileParts.add('galleryImages');
//           } else {
//             urls.add(p);
//           }
//         }
//         if (urls.isNotEmpty) {
//           request.fields['galleryImagesUrls'] = jsonEncode(urls);
//           apiKeysSent.add('galleryImagesUrls');
//         }
//         uiKeysSent.add('galleryImages');
//       }
//
//       // ---- Final debug logs (exactly what will go) ----
//       // API field keys (form-data fields)
//       Utils.showLog("🟢 API field keys to send => ${apiKeysSent.toList()}");
//       // File parts
//       Utils.showLog("📦 File parts to upload => $fileParts  (count: ${request.files.length})");
//       // UI keys (from your changedFields that actually used)
//       Utils.showLog("🟡 Updated UI keys (from changedFields) => ${uiKeysSent.toList()}");
//       // Raw snapshot (optional but handy)
//       Utils.showLog("🧾 request.fields snapshot => ${request.fields}");
//
//       // Ignored/unmapped keys (were in changedFields but not sent)
//       final ignored = changedFields.keys.toSet().difference(uiKeysSent);
//       if (ignored.isNotEmpty) {
//         Utils.showLog("⚠️ Ignored / Unmapped changedFields keys => ${ignored.toList()}");
//       }
//
//       // ---- Send ----
//       final streamed = await request.send();
//       final status = streamed.statusCode;
//       final body = await streamed.stream.bytesToString();
//
//       log("Update Response Code => $status");
//       log("Update Raw => $body");
//
//       if (status >= 200 && status < 300) {
//         dynamic decoded;
//         try {
//           decoded = jsonDecode(body);
//         } catch (_) {
//           throw StateError('Non-JSON response from server: $body');
//         }
//
//         if (decoded is String) {
//           try { decoded = jsonDecode(decoded); }
//           catch (_) { decoded = {"status": true, "message": decoded}; }
//         }
//
//         if (decoded is Map<String, dynamic>) {
//           final map = Map<String, dynamic>.from(decoded);
//           final rawData = map['data'];
//           if (rawData is String) {
//             map['message'] = (map['message'] ?? rawData).toString();
//             map['data'] = null;
//           }
//           return UpdateProductDetailResponseModel.fromJson(map);
//         }
//         throw StateError('Unexpected JSON shape: ${decoded.runtimeType}');
//       } else {
//         try {
//           final err = jsonDecode(body);
//           if (err is Map<String, dynamic>) {
//             return UpdateProductDetailResponseModel.fromJson(err);
//           }
//         } catch (_) {}
//         return null;
//       }
//     } catch (e, st) {
//       Utils.showLog("Update Ad Listing Error => $e");
//       log("STACK => $st");
//       return null;
//     }
//   }
// }


class UpdateListingApi {
  static Future<UpdateProductDetailResponseModel?> callApi({
    required String uid,
    required String adId,
    required Map<String, dynamic> changedFields,
    required List<int> removedGalleryIndexes,
  }) async {
    Utils.showLog("Update Ad Listing API Calling...");
    Utils.showLog("🔥 Diffs (incoming) => ${jsonEncode(changedFields)}");

    final token = await FirebaseAccessToken.onGet();

    try {
      final request = http.MultipartRequest('POST', Uri.parse(Api.updateListingApi));

      // headers
      request.headers.addAll({
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uid,
      });

      // required id
      request.fields[ApiParams.adId] = adId;
      request.fields['listing_id'] = adId;
      request.fields['product_id'] = adId;

      // helper
      void putField(String apiKey, dynamic value) {
        if (value == null) return;
        if (value is String && value.trim().isEmpty) return;
        request.fields[apiKey] = value.toString();
      }

      bool isLocalFilePath(String value) {
        final p = value.trim();
        if (p.isEmpty) return false;
        return p.startsWith('/') || p.startsWith('file://') || p.contains(':\\') || p.contains('data/user') || p.contains('/storage/');
      }

      // ------------ SIMPLE FIELDS ------------
      if (changedFields.containsKey('title'))                 putField(ApiParams.title,                 changedFields['title']);
      if (changedFields.containsKey('subTitle'))              putField(ApiParams.subTitle,              changedFields['subTitle']);
      if (changedFields.containsKey('description'))           putField(ApiParams.description,           changedFields['description']);

      if (changedFields.containsKey('location'))              putField(ApiParams.location,              changedFields['location']);

      if (changedFields.containsKey('price'))                 putField(ApiParams.price,                 changedFields['price']);
      if (changedFields.containsKey('minimumOffer'))          putField(ApiParams.minimumOffer,          changedFields['minimumOffer']);
      if (changedFields.containsKey('availableUnits'))        putField(ApiParams.availableUnits,        changedFields['availableUnits']);

      if (changedFields.containsKey('isOfferAllowed'))        putField(ApiParams.isOfferAllowed,        (changedFields['isOfferAllowed'] == true).toString());
      if (changedFields.containsKey('isAuctionEnabled'))      putField(ApiParams.isAuctionEnabled,      (changedFields['isAuctionEnabled'] == true).toString());
      if (changedFields.containsKey('isReservePriceEnabled')) putField(ApiParams.isReservePriceEnabled, (changedFields['isReservePriceEnabled'] == true).toString());

      if (changedFields.containsKey('attributes')) {
        request.fields[ApiParams.attributes] = jsonEncode(changedFields['attributes']);
      }

      // 🔹 NEW: saleType
      if (changedFields.containsKey('saleType'))              putField(ApiParams.saleType,              changedFields['saleType']);

      // ------------ AUCTION FIELDS ------------
      if (changedFields.containsKey('reservePriceAmount'))    putField(ApiParams.reservePriceAmount,    changedFields['reservePriceAmount']);
      if (changedFields.containsKey('auctionStartingPrice'))  putField(ApiParams.auctionStartingPrice,  changedFields['auctionStartingPrice']);
      if (changedFields.containsKey('auctionDurationDays'))   putField(ApiParams.auctionDurationDays,   changedFields['auctionDurationDays']);

      // (Optional) scheduled date
      if (changedFields.containsKey('scheduledPublishDate'))  putField(ApiParams.scheduledPublishDate,  changedFields['scheduledPublishDate']);

      // ------------ GALLERY INDEXES ------------
      if (removedGalleryIndexes.isNotEmpty) {
        request.fields[ApiParams.galleryIndexes] = jsonEncode(removedGalleryIndexes);
      } else if (changedFields.containsKey('galleryIndexes')) {
        final List<int> idxs = (changedFields['galleryIndexes'] as List?)?.map((e) => (e as num).toInt()).toList() ?? [];
        if (idxs.isNotEmpty) {
          request.fields[ApiParams.galleryIndexes] = jsonEncode(idxs);
        }
      }

      // ------------ PRIMARY IMAGE ------------
      if (changedFields.containsKey('primaryImage')) {
        final String path = (changedFields['primaryImage'] ?? '').toString();
        if (path.isNotEmpty) {
          final isLocal = isLocalFilePath(path);
          if (isLocal) {
            request.files.add(await http.MultipartFile.fromPath('primaryImage', path));
          } else {
            request.fields['primaryImage'] = path;
          }
        }
      }

      // ------------ GALLERY IMAGES ------------
      if (changedFields.containsKey('galleryImages')) {
        final List<String> all = (changedFields['galleryImages'] as List?)?.map((e) => e.toString()).toList() ?? [];
        final List<String> urls = [];
        for (final p in all) {
          final isLocal = isLocalFilePath(p);
          if (isLocal) {
            request.files.add(await http.MultipartFile.fromPath('galleryImages[]', p));
          } else {
            urls.add(p);
          }
        }
        if (urls.isNotEmpty) {
          request.fields['galleryImagesUrls'] = jsonEncode(urls);
        }
      }

      Utils.showLog("🧾 request.fields => ${request.fields}");
      Utils.showLog("📦 files => ${request.files.map((f) => f.field).toList()}");

      final streamed = await request.send();
      final status = streamed.statusCode;
      final body = await streamed.stream.bytesToString();

      log("Update Response Code => $status");
      log("Update Raw => $body");

      if (status >= 200 && status < 300) {
        dynamic decoded;
        try { decoded = jsonDecode(body); }
        catch (_) { throw StateError('Non-JSON response from server: $body'); }

        if (decoded is String) {
          try { decoded = jsonDecode(decoded); }
          catch (_) { decoded = {"status": true, "message": decoded}; }
        }

        if (decoded is Map<String, dynamic>) {
          final map = Map<String, dynamic>.from(decoded);
          map['status'] = map['status'] ?? true;
          final rawData = map['data'];
          if (rawData is String) {
            map['message'] = (map['message'] ?? rawData).toString();
            map['data'] = null;
          }
          return UpdateProductDetailResponseModel.fromJson(map);
        }
        throw StateError('Unexpected JSON shape: ${decoded.runtimeType}');
      } else {
        try {
          final err = jsonDecode(body);
          if (err is Map<String, dynamic>) {
            return UpdateProductDetailResponseModel.fromJson(err);
          }
        } catch (_) {}
        return null;
      }
    } catch (e, st) {
      Utils.showLog("Update Ad Listing Error => $e");
      log("STACK => $st");
      return null;
    }
  }
}



