import 'dart:convert';
import 'dart:developer';

import 'package:http/http.dart' as http;
import 'package:listify/ui/upload_video_screen/model/video_upload_model.dart';
import 'package:listify/utils/api.dart';
import 'package:listify/utils/api_params.dart';
import 'package:listify/utils/firebse_access_token.dart';
import 'package:listify/utils/utils.dart';
import 'package:path/path.dart';

class UploadAdVideoApi {
  static Future<VideoUploadModel?> callApi({
    required String ad,
    required String caption,
    required String videoPath,
    required String thumbnailPath,
    required String uId,
    required String duration,
  }) async {
    Utils.showLog("Submit Ad Video API Calling...");

    final token = await FirebaseAccessToken.onGet();

    try {
      var request = http.MultipartRequest(
        'POST',
        Uri.parse(Api.uploadAdVideoApi),
      );

      var headers = {
        ApiParams.key: Api.secretKey,
        ApiParams.authToken: 'Bearer $token',
        ApiParams.authUid: uId,
        ApiParams.contentType: 'application/json',
      };

      Utils.showLog("Ad Video API Headers => $headers");

      request.headers.addAll(headers);

      // Add form fields
      request.fields['ad'] = ad;
      request.fields['caption'] = caption;
      request.fields['duration'] = duration;

      // Attach video, thumbnail, imageGallery
      request.files.add(await http.MultipartFile.fromPath(
        'videoUrl',
        videoPath,
        filename: basename(videoPath),
      ));

      request.files.add(await http.MultipartFile.fromPath(
        'thumbnailUrl',
        thumbnailPath,
        filename: basename(thumbnailPath),
      ));

      // request.files.add(await http.MultipartFile.fromPath(
      //   'imageGallery',
      //   imageGalleryPath,
      //   filename: basename(imageGalleryPath),
      // ));

      Utils.showLog("Submit Ad Video Request Fields => ${videoPath}");
      Utils.showLog("Submit Ad Video Request Fields => ${thumbnailPath}");

      Utils.showLog("Submit Ad Video Request Fields => ${request.fields}");

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      log("Submit Ad Video Response Code => ${response.statusCode}");
      log("Submit Ad Video Response => $responseBody");

      if (response.statusCode == 200) {
        final jsonResponse = jsonDecode(responseBody);

        return VideoUploadModel.fromJson(jsonResponse);
      } else {
        return null;
      }
    } catch (e) {
      Utils.showLog("Submit Ad Video Error => $e");
      return null;
    }
  }
}
