<?php

/**
 * 一个简单的爬虫
 * 用于抓取开发者头条的博客
 *
 */

require_once __DIR__ . '/phpQuery/phpQuery.php';

function getUrl($date) {
    $url = "http://toutiao.io/prev/%s";
    return sprintf($url, $date);
}

function doc($url) {
    phpQuery::newDocumentFile($url);
}

function get($date) {
    doc(getUrl($date));
    $rows  = pq('.content');
    foreach ($rows as $content) {
        $a = pq($content)->find('.title a');
        createMarkdown($a->text(), $a->attr('href'));
        setLog($date);
    }
}

function setLog($date) {
    file_put_contents('toutiao.log', $date . PHP_EOL, FILE_APPEND);
}

function getLog() {
    $log = @file_get_contents('toutiao.log');
    if (empty($log)) {
        return false;
    }

    $log = explode(PHP_EOL, $log);

    return array_filter($log);
}

function createHtml($content) {
    file_put_contents('toutiao.html', $content . "<br>", FILE_APPEND);
}

function createMarkdown($text, $url) {
    $blog  = sprintf("[%s](%s)", $text, $url);
    file_put_contents('toutiao.md', $blog . "<br>", FILE_APPEND);
}

function run($days = 100) {
    $log     = getLog();
    $lastDay = date('Y-m-d');

    for ($i = 0; $i < $days; $i++) {
        $date = date('Y-m-d', strtotime($lastDay . '-' . $i . 'day'));
        // if exist
        if (!empty($log) && is_array($log) && in_array($date, $log)) {
            continue;
        }

        echo $date . "\n";
        get($date);
    }
}

// 获取过去若干天博客
run(1);


