<?php

namespace Server\Core;

class File
{

    /**
     * 递归创建目录
     * @param $dir
     * @param int $mode
     * @return bool
     */
    public static function make($dir, $mode = 0755)
    {
        if (\is_dir($dir) || \mkdir($dir, $mode, true)) {
            return true;
        }
        if (!self::make(\dirname($dir), $mode)) {
            return false;
        }
        return \mkdir($dir, $mode);
    }

    public static function scanFiles(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $files = scandir($path);
        $ret = [];
        foreach ($files as $file) {
            $newPath = $path . DIRECTORY_SEPARATOR . $file;
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (is_dir($newPath)) {
                $tmpFiles = self::scanFiles($newPath);
                array_push($ret, ...$tmpFiles);
            } else {
                $ret[] = $newPath;
            }
        }
        return $ret;
    }

    /**
     * 递归获取目录下的文件
     * @param $dir
     * @param string $filter
     * @param array $result
     * @return array|false|mixed
     */
    public static function tree( $dir, $filter = '', &$result = array() )
    {
        try {
            $files = new \DirectoryIterator($dir);
            foreach ($files as $file) {
                if ($file->isDot()) {
                    continue;
                }
                $filename = $file->getFilename();
                if ($file->isDir()) {
                    self::tree($dir . DIRECTORY_SEPARATOR . $filename, $filter, $result);

                } else {
                    if (!empty($filter) && !\preg_match($filter, $filename)) {
                        continue;
                    }
                    $result[$dir][] = $filename;
                }
            }
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 递归删除目录
     * @param $dir
     * @param $filter
     * @return bool
     */
    public static function del($dir, $filter = '')
    {
        $files = new \DirectoryIterator($dir);
        foreach ($files as $file) {
            if ($file->isDot()) {
                continue;
            }
            $filename = $file->getFilename();
            if (!empty($filter) && !\preg_match($filter, $filename)) {
                continue;
            }
            if ($file->isDir()) {
                self::del($dir . DIRECTORY_SEPARATOR . $filename);
            } else {
                \unlink($dir . DIRECTORY_SEPARATOR . $filename);
            }
        }
        return \rmdir($dir);
    }
}