class GoogleMapsRuntime {
  static bool nativeMapsEnabled = true;
  static String rendererLabel = 'unknown';

  static bool get canUseNativeMaps => nativeMapsEnabled;

  static void setRendererState({
    required bool enabled,
    required String renderer,
  }) {
    nativeMapsEnabled = enabled;
    rendererLabel = renderer;
  }
}