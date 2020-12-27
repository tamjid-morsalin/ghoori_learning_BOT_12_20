<?php

$basePath = 'https://botservice.dotlines.com.sg/tds_BOT_10_20/';
// $basePath = 'http://bot.api.test/tds_BOT_10_20/';

$sportsNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281025/5/2';
$businessNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281029/5/2';
$healthNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281033/5/2';
$artsNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281037/5/2';
$lifeStyleNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281041/5/2';
// $dsBanglaNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281045/5/2';
$openionNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281049/5/2';

$toggleNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281053/5/2';
//$travelNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281057/5/2';
$topNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/1/5';
$bookReviewNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281061/5/2';
$shoutNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281065/5/2';
$starLiveNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281069/5/2';
$lawNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281073/5/2';
$inFocusNewsDynamicCarouselApi = 'https://www.thedailystar.net/json/dynamic-news/281077/5/2';


$dynamicNewsArr = [
    'sports' => $sportsNewsDynamicCarouselApi,
    'business' => $businessNewsDynamicCarouselApi,
    'health' => $healthNewsDynamicCarouselApi,
    'art' => $artsNewsDynamicCarouselApi,
    'lifeStyle' => $lifeStyleNewsDynamicCarouselApi,
    // 'dsBangla' => $dsBanglaNewsDynamicCarouselApi,
    'opinion' => $openionNewsDynamicCarouselApi,

    'toggle' => $toggleNewsDynamicCarouselApi,
	'topnews' => $topNewsDynamicCarouselApi,
    'bookReview' => $bookReviewNewsDynamicCarouselApi,
    'shout' => $shoutNewsDynamicCarouselApi,
    'starLive' => $starLiveNewsDynamicCarouselApi,
    'law' => $lawNewsDynamicCarouselApi,
    'inFocus' => $inFocusNewsDynamicCarouselApi,
];

$redirectUrl = [
    'DSBangla' => 'https://www.thedailystar.net/bangla/',
    'ePaper' => 'http://epaper.thedailystar.net/'
];