import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:listify/utils/app_asset.dart';
import 'package:listify/utils/app_color.dart';
import 'package:listify/utils/font_style.dart';

class TimerWidget extends StatefulWidget {
  final String? endDate;
  final Color? color;
  final Color? iconColor;
  final Color? containerColor;
  final BorderRadiusGeometry? borderRadius;
  final EdgeInsetsGeometry? padding;
  final double? width;
  final double? height;

  const TimerWidget(
      {super.key, required this.endDate, this.color, this.iconColor, this.containerColor, this.borderRadius, this.padding, this.width, this.height});

  @override
  TimerWidgetState createState() => TimerWidgetState();
}

class TimerWidgetState extends State<TimerWidget> {
  Timer? _timer;
  String _remainingTime = '';

  @override
  void initState() {
    super.initState();
    _startTimer();
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  void _startTimer() {
    // Update immediately
    _updateRemainingTime();

    // Then update every second
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (mounted) {
        setState(() {
          _updateRemainingTime();
        });
      }
    });
  }

  void _updateRemainingTime() {
    if (widget.endDate == null) {
      _remainingTime = '';
      return;
    }

    try {
      final end = DateTime.parse(widget.endDate!);
      final now = DateTime.now();
      final remaining = end.difference(now);

      if (remaining.isNegative) {
        _remainingTime = 'Ended';
        _timer?.cancel(); // Stop timer when ended
        return;
      }

      if (remaining.inDays > 1) {
        _remainingTime = '${remaining.inDays} days left';
      } else if (remaining.inDays == 1) {
        _remainingTime = '1 day left';
      } else {
        final hours = remaining.inHours.remainder(24).toString().padLeft(2, '0');
        final minutes = remaining.inMinutes.remainder(60).toString().padLeft(2, '0');
        final seconds = remaining.inSeconds.remainder(60).toString().padLeft(2, '0');
        _remainingTime = '$hours:$minutes:$seconds left';
      }
    } catch (e) {
      _remainingTime = 'Invalid date';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      width: widget.width,
      padding: widget.padding,
      decoration: BoxDecoration(
          color: widget.containerColor ?? AppColors.lightRed100,
          borderRadius: widget.borderRadius ?? BorderRadius.vertical(bottom: Radius.circular(10))),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Image.asset(
            AppAsset.liveAuctionTimeIcon,
            height: 13,
            color: widget.iconColor ?? AppColors.appRedColor,
          ).paddingOnly(right: 6),
          Text(
            _remainingTime,
            style: AppFontStyle.fontStyleW700(fontColor: widget.color ?? AppColors.appRedColor, fontSize: 13),
          ),
        ],
      ),
    );
  }
}
