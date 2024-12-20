<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../config.inc.php');
require_once('../adodb5/adodb.inc.php');
require_once("../vendor/autoload.php");

require_once('./AiToPPTX/include.inc.php');

function parseTextToJson($text) {
    $lines = explode("\n", $text);
    $json = [];
    $stack = [&$json];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        // 使用正则表达式匹配层次结构
        if (preg_match('/^(\d+(\.\d+)*)\s+(.*)$/', $line, $matches)) {
            $level = count(explode('.', $matches[1]));
            $name = $matches[3];

            // 创建新的层次结构
            $newNode = ['children' => [], 'level' => $level, 'name' => $name];

            // 找到正确的父节点
            while (count($stack) > $level) {
                array_pop($stack);
            }

            // 将新节点添加到父节点的子节点中
            $parent = &$stack[count($stack) - 1];
            $parent['children'][] = $newNode;

            // 更新栈
            $stack[] = &$parent['children'][count($parent['children']) - 1];
        }
    }

    return $json;
}

$text = "如何使用AI来生成PPTX - PPT纲\n 1. 引言\n 1.1 AI生成PPTX的背景与意义\n.1.1 简述PPTX在现代工作中的重要性。\n.1.2 介绍AI技术在内容生成领域的应用现状。\n.1.3 说明AI生成PPTX的优势与潜力。\n 1.2 AI生成PPTX的基本原理\n.2.1 介绍AI生成PPTX的核心技术（如自然语言处理、图像识别等）。\n.2.2 简述AI如何理解用户需求并生成内容。\n.2.3 说明AI生成PPTX的工作流程。\n 1.3 市场与应用\n.3.1 分析AI生成PPTX的市场需求。\n.3.2 介绍AI生成PPTX在不同行业的应用案例。\n.3.3 展望AI生成PPTX的未来发展趋势。\n 2. 使用AI生成PPTX的步骤与工具\n 2.1 选择合适的AI工具\n.1.1 介绍主流的AI生成PPTX（如Microsoft PowerPoint Designer、Canva等）。\n.1.2 分析各工具的优缺点。\n.1.3 提供选择工具的建议。\n 2.2 输入与需求定义\n.2.1 说明如何清晰定义PPTX的内容需求。\n.2.2 介绍输入文本、图像等素材的方法。\n.2.3 提供需求定义的最佳实践。\n 2.3 生成与优化\n.3.1 介绍AI生成PPTX的基本步骤。\n.3.2 说明如何优化生成的PPTX内容（如布局、配色、字体等。\n.3.3 提供优化技巧与案例。\n 2.4 输出与分享\n.4.1 介绍如何导出AI生成的PPTX文件。\n.4.2 说明分享PPTX的不同方式（如邮件、云存储、在线演示等）。\n.4.3 提供输出与分享的最佳实践。\n 3. 案例分析与实践\n 3.1 成功案例分析\n.1.1 介绍AI生成PPTX的成功应用案例。\n.1.2 分析案例中的关键成功因素。\n.1.3 总结案例中的经验教训。\n 3.2 常见问题与解决方案\n.2.1 列举使用AI生成PPTX时常见的问题。\n.2.2 提供针对每个问题的解决方案。\n.2.3 说明如何避免常见问题。\n 3.3 未来展望与建议\n.3.1 展望AI生成PPTX技术的未来发展方向。\n.3.2 提供使用AI生成PPTX的建议与最佳实践。\n.3.3 总结AI生成PPTX的潜力与挑战。\n\n大纲涵盖了AI生成PPTX的背景、原理、工具使用、案例分析及未来展望，适合用于行业总结性报告的PPT展示。";

$json = parseTextToJson($text);
echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

exit;




// 导入原始数据
$JsonContent      	= file_get_contents("./json/0001.json");
$JsonData          	= json_decode($JsonContent, true);
//print_R($JsonData);exit;

$TargetCacheDir 		= realpath("./cache");
$TargetPptxFilePath = './output/0001.pptx';

AiToPptx_MakePptx($JsonData, $TargetCacheDir, $TargetPptxFilePath)

?>
