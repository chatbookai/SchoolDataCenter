<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_SaveBase64ImageToFile($base64_string, $output_file) {
    // 检查并移除"data:image/jpeg;base64,"或"data:image/png;base64,"前缀
    if (strpos($base64_string, 'base64,') !== false) {
        $base64_string = explode('base64,', $base64_string)[1];
    }
    // 将Base64解码为二进制数据
    $image_data = base64_decode($base64_string);
    // 检查解码是否成功
    if ($image_data === false) {
        echo "Base64解码失败。\n";
        return false;
    }
    // 将二进制数据写入文件
    if (file_put_contents($output_file, $image_data) === false) {
        echo "文件写入失败。\n"; print $output_file;
        return false;
    }
    //echo "文件保存成功：$output_file\n";
    return true;
}

function AiToPptx_DeleteCacheDirectory($dir) {
  if (!file_exists($dir)) {
      return true;
  }
  if (!is_dir($dir)) {
      return unlink($dir);
  }
  foreach (scandir($dir) as $item) {
      if ($item == '.' || $item == '..') {
          continue;
      }
      if (!AiToPptx_DeleteCacheDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
          return false;
      }
  }
  return rmdir($dir);
}

function AiToPptx_CreateZip($source, $destination) {
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    if(is_file($destination))  {
      unlink($destination);
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        return false;
    }

    $source = realpath($source);

    // 如果是文件夹，递归添加其中的文件和文件夹
    if (is_dir($source)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $file) {
            $filePath = realpath($file);
            $relativePath = substr($filePath, strlen($source) + 1);

            if (is_dir($filePath)) {
                $zip->addEmptyDir($relativePath);
            } else if (is_file($filePath)) {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
    else if (is_file($source)) {
        // 如果是单个文件，直接添加
        $zip->addFile($source, basename($source));
    }

    $zip->close();

    //删除缓存的过程文件
    global $AiToPptx_DeleteCacheDirectory_Status;
    if($AiToPptx_DeleteCacheDirectory_Status) {
      AiToPptx_DeleteCacheDirectory($source);
    }

    return true;
}

function AiToPptx_NumberToColor($color) {
	// 提取 RGB 部分
	$realColor = $color & 0xFFFFFF; // 获取 RGB 部分

	// 提取红色、绿色和蓝色通道
	$r = ($realColor >> 16) & 0xFF; // 红色通道
	$g = ($realColor >> 8) & 0xFF;  // 绿色通道
	$b = $realColor & 0xFF;         // 蓝色通道

	// 格式化为两位十六进制并连接
	$hexColor = sprintf("%02X%02X%02X", $r, $g, $b);

	return $hexColor;
}

?>
