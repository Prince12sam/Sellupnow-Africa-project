import 'package:flutter/cupertino.dart';

class CommonCupertinoSwitch extends StatelessWidget {
  final bool value;
  final ValueChanged<bool> onChanged;
  final Color activeColor;
  final Color trackColor;
  final double scale;

  const CommonCupertinoSwitch({
    super.key,
    required this.value,
    required this.onChanged,
    this.activeColor = CupertinoColors.activeGreen,
    this.trackColor = CupertinoColors.systemGrey,
    this.scale = 1.0,
  });

  @override
  Widget build(BuildContext context) {
    return Transform.scale(
      scale: scale,
      child: CupertinoSwitch(
        value: value,
        onChanged: onChanged,
        activeTrackColor: activeColor,
        inactiveTrackColor: trackColor,
      ),
    );
  }
}
