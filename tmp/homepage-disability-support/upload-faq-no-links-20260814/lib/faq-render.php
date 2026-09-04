<?php

function renderFaqAnswerWithoutLinks($html)
{
    $answerHtml = (string) $html;
    $answerWithoutLinks = preg_replace('#<a\b[^>]*>(.*?)</a>#isu', '$1', $answerHtml);

    return is_string($answerWithoutLinks) ? $answerWithoutLinks : $answerHtml;
}
