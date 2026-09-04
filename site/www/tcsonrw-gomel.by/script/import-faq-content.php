<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script is available from the command line only.\n");
    exit(1);
}

$sourcePath = $argv[1] ?? '';
$outputPath = $argv[2] ?? '';

if ($sourcePath === '' || $outputPath === '' || !is_file($sourcePath)) {
    fwrite(STDERR, "Usage: php import-faq-content.php SOURCE_HTML OUTPUT_PHP\n");
    exit(1);
}

libxml_use_internal_errors(true);

$sourceHtml = file_get_contents($sourcePath);
$sourceDocument = new DOMDocument('1.0', 'UTF-8');
$sourceDocument->loadHTML(
    '<?xml encoding="UTF-8">' . $sourceHtml,
    LIBXML_NOERROR | LIBXML_NOWARNING
);
$xpath = new DOMXPath($sourceDocument);
$itemNodes = $xpath->query(
    '//div[contains(concat(" ", normalize-space(@class), " "), " faq__item ")]'
);

function normalizeFaqText($value)
{
    $value = str_replace("\xc2\xa0", ' ', (string) $value);
    $value = preg_replace('/\s+/u', ' ', $value);

    return trim((string) $value);
}

function shouldSkipFaqImportElement(DOMElement $element)
{
    if (in_array($element->tagName, array('script', 'style', 'img', 'iframe', 'svg', 'noscript'), true)) {
        return true;
    }

    $classPrefixes = array('heateor_', 'events-calendar-news');
    $classes = preg_split('/\s+/u', trim($element->getAttribute('class'))) ?: array();

    foreach ($classes as $className) {
        foreach ($classPrefixes as $prefix) {
            if (strpos($className, $prefix) === 0) {
                return true;
            }
        }
    }

    return false;
}

function appendSanitizedFaqNode(DOMNode $sourceNode, DOMNode $targetParent, DOMDocument $targetDocument)
{
    if ($sourceNode instanceof DOMText) {
        $text = str_replace("\xc2\xa0", ' ', $sourceNode->nodeValue);
        if (trim($text) !== '') {
            $targetParent->appendChild($targetDocument->createTextNode($text));
        }

        return;
    }

    if (!($sourceNode instanceof DOMElement) || shouldSkipFaqImportElement($sourceNode)) {
        return;
    }

    $allowedTags = array(
        'p' => 'p',
        'ul' => 'ul',
        'ol' => 'ol',
        'li' => 'li',
        'strong' => 'strong',
        'b' => 'strong',
        'em' => 'em',
        'i' => 'em',
        'a' => 'a',
        'br' => 'br',
        'blockquote' => 'blockquote',
    );
    $targetTag = $allowedTags[$sourceNode->tagName] ?? null;

    if ($targetTag === null) {
        foreach ($sourceNode->childNodes as $childNode) {
            appendSanitizedFaqNode($childNode, $targetParent, $targetDocument);
        }

        return;
    }

    $targetElement = $targetDocument->createElement($targetTag);

    if ($targetTag === 'a') {
        $href = trim($sourceNode->getAttribute('href'));
        if (!preg_match('#^(https?://|mailto:|tel:)#i', $href)) {
            foreach ($sourceNode->childNodes as $childNode) {
                appendSanitizedFaqNode($childNode, $targetParent, $targetDocument);
            }

            return;
        }

        $targetElement->setAttribute('href', $href);
        if (preg_match('#^https?://#i', $href)) {
            $targetElement->setAttribute('target', '_blank');
            $targetElement->setAttribute('rel', 'noopener noreferrer');
        }
    }

    foreach ($sourceNode->childNodes as $childNode) {
        appendSanitizedFaqNode($childNode, $targetElement, $targetDocument);
    }

    if ($targetTag === 'br' || normalizeFaqText($targetElement->textContent) !== '') {
        $targetParent->appendChild($targetElement);
    }
}

function getFaqImportInnerHtml(DOMElement $answerNode)
{
    $cleanDocument = new DOMDocument('1.0', 'UTF-8');
    $root = $cleanDocument->createElement('div');
    $cleanDocument->appendChild($root);

    foreach ($answerNode->childNodes as $childNode) {
        appendSanitizedFaqNode($childNode, $root, $cleanDocument);
    }

    $html = '';
    foreach ($root->childNodes as $childNode) {
        $html .= $cleanDocument->saveHTML($childNode);
    }

    return trim($html);
}

$faqItems = array();

foreach ($itemNodes as $itemNode) {
    if (!($itemNode instanceof DOMElement)) {
        continue;
    }

    $titleNode = $xpath->query(
        './/div[contains(concat(" ", normalize-space(@class), " "), " faq__item-title ")]',
        $itemNode
    )->item(0);
    $answerNode = $xpath->query(
        './/div[contains(concat(" ", normalize-space(@class), " "), " faq__item-answer ")]',
        $itemNode
    )->item(0);

    if (!($titleNode instanceof DOMElement) || !($answerNode instanceof DOMElement)) {
        continue;
    }

    $question = normalizeFaqText($titleNode->textContent);
    $answerHtml = getFaqImportInnerHtml($answerNode);

    if ($question === '' || $answerHtml === '') {
        continue;
    }

    $faqItems[] = array(
        'question' => $question,
        'answer_html' => $answerHtml,
    );
}

$generated = "<?php\n\n";
$generated .= "// Generated from the approved FAQ source file by script/import-faq-content.php.\n";
$generated .= "return " . var_export($faqItems, true) . ";\n";

if (file_put_contents($outputPath, $generated) === false) {
    fwrite(STDERR, "Unable to write the FAQ data file.\n");
    exit(1);
}

fwrite(STDOUT, 'Imported FAQ items: ' . count($faqItems) . "\n");
