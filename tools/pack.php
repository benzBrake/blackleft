<?php
/**
 * Typecho 项目打包脚本
 * 将项目文件打包为带时间戳的 tar.gz 文件
 */

// 1. 配置部分
define('PROJECT_ROOT', dirname(__DIR__));
define('EXCLUDE_FILE', __DIR__ . '/pack.exclude');

/**
 * 读取排除列表
 * 
 * @return array 排除规则数组
 */
function readExcludeList() {
    $excludeList = [];
    
    if (file_exists(EXCLUDE_FILE)) {
        $lines = file(EXCLUDE_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '#') !== 0) {
                $excludeList[] = $line;
            }
        }
    }
    
    return $excludeList;
}

/**
 * 检查文件是否应该被排除
 * 
 * @param string $filePath 文件路径（相对于项目根目录）
 * @param array $excludeList 排除规则数组
 * @return bool 是否应该排除
 */
function shouldExclude($filePath, $excludeList) {
    // 标准化路径分隔符
    $filePath = str_replace('\\', '/', $filePath);
    
    foreach ($excludeList as $pattern) {
        $pattern = str_replace('\\', '/', $pattern);
        
        // 直接匹配
        if ($filePath === $pattern || $filePath === ltrim($pattern, './')) {
            return true;
        }
        
        // 目录匹配（检查路径是否以排除目录开头）
        if (strpos($filePath, $pattern . '/') === 0 || strpos($filePath, '/' . $pattern . '/') !== false) {
            return true;
        }
        
        // 检查路径的任何部分是否匹配排除模式
        $parts = explode('/', $filePath);
        foreach ($parts as $part) {
            if ($part === $pattern) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * 递归获取文件列表
 * 
 * @param string $dir 目录路径
 * @param array $excludeList 排除规则数组
 * @param string $baseDir 基础目录（用于计算相对路径）
 * @return array 符合条件的文件列表
 */
function getAllFiles($dir, $excludeList, $baseDir = null) {
    $files = [];
    
    if ($baseDir === null) {
        $baseDir = $dir;
    }
    
    // 检查目录本身是否应该排除
    $relativePath = str_replace('\\', '/', substr($dir, strlen($baseDir) + 1));
    if (!empty($relativePath) && shouldExclude($relativePath, $excludeList)) {
        return $files;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
        $relativePath = str_replace('\\', '/', substr($fullPath, strlen($baseDir) + 1));
        
        // 检查是否应该排除
        if (shouldExclude($relativePath, $excludeList)) {
            continue;
        }
        
        if (is_dir($fullPath)) {
            // 递归处理子目录
            $files = array_merge($files, getAllFiles($fullPath, $excludeList, $baseDir));
        } else {
            // 添加文件到列表
            $files[] = [
                'relative' => $relativePath,
                'absolute' => $fullPath
            ];
        }
    }
    
    return $files;
}

/**
 * 创建压缩包
 * 
 * @param array $files 文件列表
 * @param string $archiveName 压缩包名称
 * @return bool 是否成功
 */
function createArchive($files, $archiveName) {
    try {
        // 使用 PharData 创建 tar 压缩包
        $tarFile = PACK_DIR . '/' . str_replace('.gz', '', $archiveName);
        $phar = new PharData($tarFile);
        
        // 先添加所有文件到 tar
        echo "正在添加文件到 tar 包...\n";
        $count = 0;
        $total = count($files);
        
        foreach ($files as $file) {
            $phar->addFile($file['absolute'], $file['relative']);
            $count++;
            
            // 显示进度
            if ($count % 50 === 0 || $count === $total) {
                echo "已处理: $count/$total 文件\n";
            }
        }
        
        // 然后压缩完整的 tar 文件
        echo "正在压缩 tar 文件为 gz 格式...\n";
        $phar->compress(Phar::GZ);
        
        echo "压缩包创建完成: $archiveName\n";
        return true;
        
    } catch (Exception $e) {
        echo "创建压缩包失败: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * 主函数
 */
function main() {
    echo "=== Typecho 项目打包工具 ===\n";
    
    // 生成时间戳
    $timestamp = date('Y-m-d-His');
    $archiveName = "pack-$timestamp.tar.gz";
    
    echo "目标压缩包: $archiveName\n";
    
    // 读取排除列表
    echo "读取排除列表...\n";
    $excludeList = readExcludeList();
    echo "排除规则数量: " . count($excludeList) . "\n";
    
    // 获取文件列表
    echo "扫描项目文件...\n";
    $files = getAllFiles(PROJECT_ROOT, $excludeList);
    echo "找到文件数量: " . count($files) . "\n";
    
    if (empty($files)) {
        echo "没有找到要打包的文件！\n";
        return;
    }
    
    // 创建压缩包
    echo "开始创建压缩包...\n";
    if (createArchive($files, $archiveName)) {
        echo "打包完成！\n";
        echo "压缩包位置: " . PACK_DIR . "/$archiveName\n";
        
        // 显示压缩包大小
        $archivePath = PACK_DIR . "/$archiveName";
        if (file_exists($archivePath)) {
            $size = filesize($archivePath);
            $sizeMb = round($size / 1024 / 1024, 2);
            echo "压缩包大小: {$sizeMb} MB\n";
        }

        // 删除临时文件
        echo "删除无用tar文件...\n";
        unlink(PACK_DIR . '/' . str_replace('.gz', '', $archiveName));
    } else {
        echo "打包失败！\n";
    }
}

// 检查 Phar 扩展是否可用
if (!extension_loaded('phar')) {
    echo "错误: Phar 扩展未安装，无法创建压缩包\n";
    exit(1);
}

$pack_dir = PROJECT_ROOT . '/pack';
if (!is_dir($pack_dir)) {
    mkdir($pack_dir, 0755, true);
}
define('PACK_DIR', $pack_dir);
// 执行主函数
main();