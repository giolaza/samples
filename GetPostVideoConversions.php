<?php

namespace App\Actions;

use FFMpeg\FFMpeg;

class GetPostVideoConversions
{
    const MAX_BITRATE = 5000;

    const MAX_WIDTH = 1920;

    const MAX_HEIGHT = 1080;

    /**
     * @param $mediaPath
     *
     * @return \Illuminate\Support\Collection|void
     */
    public function handle($mediaPath)
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'timeout' => 3600,
        ]);

        $conversions = collect([]);

        $videoFormat = $ffmpeg->getFFProbe()->format($mediaPath);

        $stream = $ffmpeg->getFFProbe()
            ->streams($mediaPath)
            ->videos()
            ->first();

        if (!$stream) {
            return $conversions;
        }

        $dimensions = $stream->getDimensions();

        if (strtolower($stream->get('codec_name')) != 'h264') {
            $conversions->push(['name' => 'codec']);
        }

        if ($stream->get('bits_per_raw_sample') > 8) {
            $conversions->push(['name' => 'bits_per_raw_sample']);
        }

        if ($dimensions->getWidth() > self::MAX_WIDTH || $dimensions->getHeight() > self::MAX_HEIGHT) {
            $conversions->push(['name' => 'dimensions', 'width' => self::MAX_WIDTH, 'height' => self::MAX_HEIGHT]);
        }

        $bitRate = $videoFormat->get('bit_rate') / 1000;

        if ($bitRate > self::MAX_BITRATE || $bitRate == 0) {
            $conversions->push(['name' => 'bitrate', 'bitrate' => min(self::MAX_BITRATE, $bitRate)]);
        }

        return $conversions;
    }
}
