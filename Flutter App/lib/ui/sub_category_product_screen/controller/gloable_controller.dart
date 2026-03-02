import 'package:get/get.dart';
import 'package:listify/utils/utils.dart';

// class GlobalController extends GetxController {
//   static final RxMap<String, dynamic> locationData = <String, dynamic>{}.obs;
//
//   static void updateLocation(Map<String, dynamic> data) {
//     locationData.value = data;
//     Utils.showLog("Global location updated:::::::::::::: $data");
//     Utils.showLog("Global location updated: ${locationData["selectedCity"]}");
//     // Utils.showLog("Global location updated: ${locationData["locationData"]['fullAddress']}");
//   }
// }


/*class GlobalController extends GetxController {
  // Reactive global map
  static final RxMap<String, dynamic> locationData = <String, dynamic>{}.obs;

  static void updateLocation(Map<String, dynamic> data) {
    // This updates keys reactively without replacing the RxMap instance
    locationData.assignAll(data);
    locationData.refresh();
    Utils.showLog("Global location updated:::::::::::::: $data");
    Utils.showLog("Global location city: ${locationData['selectedCity']}");
  }
}*/
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:listify/utils/utils.dart';

class GlobalController extends GetxController {
  static final GetStorage _box = GetStorage();

  // 🔁 Reactive selected (persisted) location
  static final RxMap<String, dynamic> locationData = <String, dynamic>{}.obs;
  static final RxBool hasSelectedLocation = false.obs;

  // 🗝️ Storage keys
  static const String _kSelectedLocation = 'selectedLocation';
  static const String _kHasSelected = 'hasSelectedLocation';

  /// 📦 main() માં GetStorage.init() બાદ call કરવું
  static Future<void> init() async {
    final Map<String, dynamic>? saved =
    _box.read<Map<String, dynamic>>(_kSelectedLocation);
    final bool savedHasSelected = _box.read<bool>(_kHasSelected) ?? false;

    if (savedHasSelected && (saved != null && saved.isNotEmpty)) {
      locationData.assignAll(saved);
      hasSelectedLocation.value = true;
      Utils.showLog("Loaded persisted selectedLocation: $saved");
    } else {
      locationData.clear();
      hasSelectedLocation.value = false;
    }
  }

  /// 🔧 તમારી screenમાંથી આવતા field names normalize કરી દઈએ
  static Map<String, dynamic> _normalize(Map<String, dynamic> raw) {
    // તમારા args: homeSelectedCity / homeSelectedState / homeSelectedCountry
    // અને future-proof: selectedCity / selectedState / selectedCountry
    final city = (raw['homeSelectedCity'] ?? raw['selectedCity'] ?? '').toString().trim();
    final state = (raw['homeSelectedState'] ?? raw['selectedState'] ?? '').toString().trim();
    final country = (raw['homeSelectedCountry'] ?? raw['selectedCountry'] ?? '').toString().trim();
    final fullAddress = (raw['fullAddress'] ?? '').toString().trim();

    final lat = raw['latitude'];
    final lng = raw['longitude'];

    return <String, dynamic>{
      'selectedCity': city,
      'selectedState': state,
      'selectedCountry': country,
      'fullAddress': fullAddress,
      'latitude': lat,
      'longitude': lng,
    }..removeWhere((k, v) => v == null); // null keys clean
  }

  /// ✅ Public: user selection set + persist
  static void setSelectedLocation(Map<String, dynamic> data) {
    final normalized = _normalize(data);
    locationData.assignAll(normalized);
    hasSelectedLocation.value = true;

    _box.write(_kSelectedLocation, Map<String, dynamic>.from(locationData));
    _box.write(_kHasSelected, true);

    Utils.showLog("Selected location saved: $locationData");
  }

  /// (optional) GPS પર પાછું જવું હોય તો
  static void clearSelectedLocation() {
    locationData.clear();
    hasSelectedLocation.value = false;
    _box.remove(_kSelectedLocation);
    _box.write(_kHasSelected, false);
    Utils.showLog("Selected location cleared → Using GPS only.");
  }

  /// 🧩 Text builder (fullAddress > city,state,country)
  static String buildSelectedText() {
    final ga = (locationData['fullAddress'] as String?)?.trim() ?? '';
    if (ga.isNotEmpty) return ga;

    final gc = (locationData['selectedCity'] as String?)?.trim() ?? '';
    final gs = (locationData['selectedState'] as String?)?.trim() ?? '';
    final gcn = (locationData['selectedCountry'] as String?)?.trim() ?? '';
    return [gc, gs, gcn].where((e) => e.isNotEmpty).join(', ');
  }

  /// 🔁 Backward compatibility (if you still call updateLocation)
  static void updateLocation(Map<String, dynamic> data) {
    setSelectedLocation(data);
  }
}
