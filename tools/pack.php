<?php
/**
 * Typecho 项目打包脚本
 * 获取 @version 信息并打包为 zip 文件
 * 
 * 使用方式：
 * 1. 在下面定义 PACKAGE_NAME 常量来强制定义包名（可选）
 *    define('PACKAGE_NAME', 'YourPackageName');
 * 2. 运行脚本
 */

// 1. 配置部分
define('PROJECT_ROOT', dirname(__DIR__));
define('EXCLUDE_FILE', __DIR__ . '/pack.exclude');

// 在这里强制定义包名（可选）
// define('PACKAGE_NAME', 'YourPackageName');

/**
 * 从文件头部注释中获取 @version 信息
 * 
 * @param string $filePath 文件路径
 * @return string 版本信息
 */
function getVersionInfo($filePath) {
    if (!file_exists($filePath)) {
        return '';
    }
    
    $content = file_get_contents($filePath);
    
    // 使用正则表达式匹配 @version
    if (preg_match('/@version\s+([^\n\r]+)/', $content, $matches)) {
        return trim($matches[1]);
    }
    
    return '';
}

/**
 * 获取项目的包信息
 * 
 * @return array 包含 package 和 version 信息的数组
 * @throws Exception 如果无法获取到必要的包信息
 */
function getProjectPackageInfo() {
    $package = '';
    
    // 检查是否已经定义了 PACKAGE_NAME 常量
    if (defined('PACKAGE_NAME')) {
        echo "使用强制定义的包名: " . PACKAGE_NAME . "\n";
        $package = PACKAGE_NAME;
    } else {
        echo "未定义 PACKAGE_NAME 常量，使用项目目录名作为包名\n";
        
        // 获取项目目录名
        $projectDir = basename(PROJECT_ROOT);
        $package = $projectDir;
        
        echo "项目目录名: " . $package . "\n";
    }
    
    // 获取版本信息
    $version = '';
    
    // 优先尝试从 index.php 获取
    $indexFile = PROJECT_ROOT . '/index.php';
    if (file_exists($indexFile)) {
        $version = getVersionInfo($indexFile);
    }
    
    // 其次尝试从 Plugin.php 获取
    if (empty($version)) {
        $pluginFile = PROJECT_ROOT . '/Plugin.php';
        if (file_exists($pluginFile)) {
            $version = getVersionInfo($pluginFile);
        }
    }
    
    // 如果没有获取到版本信息，抛出异常
    if (empty($version)) {
        throw new Exception("错误: 无法获取版本信息。请确保 index.php 或 Plugin.php 中包含 @version 注释");
    }
    
    return [
        'package' => $package,
        'version' => $version
    ];
}

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
 * 创建 ZIP 压缩包
 * 
 * @param array $files 文件列表
 * @param string $archiveName 压缩包名称
 * @param string $packageDir 包名（作为压缩包内的顶层目录）
 * @return bool 是否成功
 */
function createZipArchive($files, $archiveName, $packageDir) {
    try {
        $zipFile = PACK_DIR . '/' . $archiveName;
        
        // 检查文件是否已存在
        if (file_exists($zipFile)) {
            echo "发现同名压缩包，将进行覆盖: $zipFile\n";
        }
        
        $zip = new ZipArchive();
        
        // 使用 OVERWRITE 标志确保覆盖现有文件
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            echo "无法创建 ZIP 文件: $zipFile\n";
            return false;
        }
        
        echo "正在添加文件到 ZIP 包...\n";
        echo "所有文件将被放在 '$packageDir' 目录下\n";
        
        $count = 0;
        $total = count($files);
        
        foreach ($files as $file) {
            // 添加文件到 ZIP，前面加上包目录
            $zipPath = $packageDir . '/' . $file['relative'];
            $zip->addFile($file['absolute'], $zipPath);
            $count++;
            
            // 显示进度
            if ($count % 50 === 0 || $count === $total) {
                echo "已处理: $count/$total 文件\n";
            }
        }
        
        // 关闭 ZIP 文件
        $zip->close();
        
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
    
    try {
        // 获取项目包信息
        echo "读取项目信息...\n";
        $packageInfo = getProjectPackageInfo();
        
        echo "包名: " . $packageInfo['package'] . "\n";
        echo "版本: " . $packageInfo['version'] . "\n";
        
        // 生成压缩包名称（仅使用年月日）
        $timestamp = date('Ymd');
        $archiveName = "{$packageInfo['package']}-{$packageInfo['version']}-$timestamp.zip";
        
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
        if (createZipArchive($files, $archiveName, $packageInfo['package'])) {
            echo "打包完成！\n";
            echo "压缩包位置: " . PACK_DIR . "/$archiveName\n";
            
            // 显示压缩包大小
            $archivePath = PACK_DIR . "/$archiveName";
            if (file_exists($archivePath)) {
                $size = filesize($archivePath);
                $sizeMb = round($size / 1024 / 1024, 2);
                echo "压缩包大小: {$sizeMb} MB\n";
            }
        } else {
            echo "打包失败！\n";
        }
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
        exit(1);
    }
}

// 检查 Zip 扩展是否可用
if (!extension_loaded('zip')) {
    echo "错误: Zip 扩展未安装，无法创建压缩包\n";
    exit(1);
}

$pack_dir = PROJECT_ROOT . '/pack';
if (!is_dir($pack_dir)) {
    mkdir($pack_dir, 0755, true);
}
define('PACK_DIR', $pack_dir);

// 执行主函数
main();
