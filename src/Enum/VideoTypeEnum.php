<?php

declare(strict_types=1);

namespace App\Enum;

enum VideoTypeEnum: string
{
    case EXTERN = 'extern';
    case INTEGRATION = 'integration';
    case MP4 = 'mp4';
    case DOUYIN = 'douyin';
    case TIKTOK = 'tiktok';
    case INSTAGRAM = 'instagram';
    case YOUTUBE_SHORT = 'youtube_short';
    case DAILYMOTION = 'dailymotion';
    case REDDIT = 'reddit';
    case TWITTER = 'twitter';
}
