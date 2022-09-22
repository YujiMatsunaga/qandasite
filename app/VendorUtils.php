<?php
namespace Novus;

require_once __DIR__ . "/../vendor/cebe/markdown/inline/UrlLinkTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/inline/StrikeoutTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/inline/LinkTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/inline/EmphStrongTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/inline/CodeTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/block/CodeTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/block/FencedCodeTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/block/TableTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/block/RuleTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/block/QuoteTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/block/ListTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/block/HtmlTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/block/HeadlineTrait.php";
require_once __DIR__ . "/../vendor/cebe/markdown/Parser.php";
require_once __DIR__ . "/../vendor/cebe/markdown/Markdown.php";
require_once __DIR__ . "/../vendor/cebe/markdown/MarkdownExtra.php";

use cebe\markdown\MarkdownExtra;

class VendorUtils
{
    public static function markDown($message)
    {
        $converter = new \cebe\markdown\MarkdownExtra();
        $message = $converter->parse($message);
        return $message;
    }
}
