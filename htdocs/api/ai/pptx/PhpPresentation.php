<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Slide\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\Drawing\Image;
use PhpOffice\PhpPresentation\Shape\Chart;

$inputFile = './json/0001.pptx'; // 指定要读取的 PPTX 文件路径

// 加载 PPTX 文件
$presentation = IOFactory::load($inputFile);

// 遍历每一页（幻灯片）
foreach ($presentation->getAllSlides() as $slideIndex => $slide) {
    echo "Slide " . ($slideIndex + 1) . ":" . PHP_EOL;

    // 遍历幻灯片中的每个元素
    foreach ($slide->getShapeCollection() as $shapeIndex => $shape) {
        echo "  Element " . ($shapeIndex + 1) . ": ";

        // 判断元素的类型，并输出对应的内容
        if ($shape instanceof RichText) {
            // 处理文本元素
            echo "Text: ";
            foreach ($shape->getParagraphs() as $paragraph) {
                echo $paragraph->getPlainText() . PHP_EOL;
            }

            // 遍历段落中的每个 TextElement（文本块）
            foreach ($shape->getParagraphs() as $paragraphIndex => $paragraph) {
                foreach ($paragraph->getRichTextElements() as $textElementIndex => $textElement) {
                    if ($textElement instanceof \PhpOffice\PhpPresentation\Shape\RichText\TextElement) {
                        $originalText = $textElement->getText();  // 获取原文本内容
                        echo "Original Text: $originalText" . PHP_EOL;

                        // 替换为新文本（你可以根据需求设置动态内容）
                        $newText = str_replace('01', '01-WANG', $originalText);  // 示例替换
                        $textElement->setText($newText);  // 只修改文本，保留原有样式
                    }
                }
            }
        } elseif ($shape instanceof Image) {
            // 处理图片元素
            echo "Image: " . $shape->getPath() . PHP_EOL;
        } elseif ($shape instanceof Chart) {
            // 处理图表元素
            echo "Chart" . PHP_EOL;
        } else {
            echo "Unknown element type" . PHP_EOL;
        }
    }
    echo PHP_EOL;
}

$outputFile = './json/0002.pptx';
$oWriter = IOFactory::createWriter($presentation, 'PowerPoint2007');
$oWriter->save($outputFile);

?>
