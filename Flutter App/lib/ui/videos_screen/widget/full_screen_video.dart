import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:video_player/video_player.dart';
import 'package:get/get.dart';

import '../../../utils/constant.dart';
import '../controller/videos_screen_controller.dart';

class FullscreenReelPage extends StatefulWidget {
  final int index;
  const FullscreenReelPage({super.key, required this.index});

  @override
  State<FullscreenReelPage> createState() => _FullscreenReelPageState();
}

class _FullscreenReelPageState extends State<FullscreenReelPage> {
  late final VideosScreenController controller;

  @override
  void initState() {
    super.initState();
    controller = Get.find<VideosScreenController>();

    // 🔒 Hide status/navigation bars (immersive)
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);

    // Optional: allow landscape while fullscreen
    SystemChrome.setPreferredOrientations(const [
      DeviceOrientation.portraitUp,
      DeviceOrientation.landscapeLeft,
      DeviceOrientation.landscapeRight,
    ]);
  }

  @override
  void dispose() {
    // Restore UI + orientation
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
    SystemChrome.setPreferredOrientations(const [
      DeviceOrientation.portraitUp,
    ]);
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final VideoPlayerController? vc = controller.videoControllers[widget.index];

    return Scaffold(
      backgroundColor: Colors.black,
      body: GestureDetector(
        onTap: () {
          // Tap to play/pause
          controller.togglePlayPause(widget.index);
          HapticFeedback.lightImpact();
        },
        child: Stack(
          children: [
            // Centered video
            Center(
              child: (vc != null && vc.value.isInitialized)
                  ? AspectRatio(
                aspectRatio: vc.value.aspectRatio,
                child: VideoPlayer(vc),
              )
                  : const CircularProgressIndicator(color: Colors.white),
            ),

            // close button (top-left)
            Positioned(
              top: MediaQuery.of(context).padding.top + 12,
              left: 12,
              child: _CircleIconButton(
                icon: Icons.close,
                onTap: () => Get.back(),
              ),
            ),

            // play/pause hint (when paused)
            GetBuilder<VideosScreenController>(
              id: Constant.idAllAds,
              builder: (_) {
                final isPlaying =
                    controller.isPlayingMap[widget.index] ?? false;
                return AnimatedOpacity(
                  opacity: isPlaying ? 0.0 : 1.0,
                  duration: const Duration(milliseconds: 150),
                  child: Center(
                    child: Container(
                      padding: const EdgeInsets.all(16),
                      decoration: const BoxDecoration(
                        color: Colors.black54,
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(Icons.play_arrow,
                          color: Colors.white, size: 36),
                    ),
                  ),
                );
              },
            ),
          ],
        ),
      ),
    );
  }
}

class _CircleIconButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback onTap;
  const _CircleIconButton({required this.icon, required this.onTap, super.key});

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white.withOpacity(0.15),
      shape: const CircleBorder(),
      child: InkWell(
        customBorder: const CircleBorder(),
        onTap: onTap,
        child: const Padding(
          padding: EdgeInsets.all(10),
          child: Icon(Icons.close, color: Colors.white, size: 22),
        ),
      ),
    );
  }
}
