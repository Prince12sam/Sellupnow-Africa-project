# listify

A new Flutter project.

## Production setup

Mobile secrets are local-only and must not be committed.

Android:

- Put the Google Maps key in `android/local.properties`:

```properties
google.maps.api.key=YOUR_ANDROID_MAPS_KEY
```

- Keep release signing data in `android/key.properties` based on `android/key.properties.example`.
- Keep Firebase config in local files only:
	- `android/app/google-services.json`
	- `google-services.json`

iOS:

- Set `GOOGLE_MAPS_API_KEY` in your Xcode build settings or `ios/Flutter/Debug.xcconfig` and `ios/Flutter/Release.xcconfig`.
- Keep Firebase config local only:
	- `ios/GoogleService-Info.plist`

Release notes:

- Native Google Maps is disabled automatically on Android emulators and other unsafe renderer environments.
- Android cleartext traffic is disabled for production.
- Release builds now enable shrinking and minification, so validate `flutter build apk --release` and iOS archive builds before store submission.

## Getting Started

This project is a starting point for a Flutter application.

A few resources to get you started if this is your first Flutter project:

- [Lab: Write your first Flutter app](https://docs.flutter.dev/get-started/codelab)
- [Cookbook: Useful Flutter samples](https://docs.flutter.dev/cookbook)

For help getting started with Flutter development, view the
[online documentation](https://docs.flutter.dev/), which offers tutorials,
samples, guidance on mobile development, and a full API reference.
