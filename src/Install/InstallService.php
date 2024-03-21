<?php

namespace Shopwwi\Admin\Install;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class InstallService
{

    /**
     * 创建数据表
     */
    public static function CreateTable()
    {
        if(! Db::connection()->getSchemaBuilder()->hasTable('migrations')){
            self::createMigrationsTable();
        }
        $path = shopwwiAdminPath(). DIRECTORY_SEPARATOR .'Install'. DIRECTORY_SEPARATOR .'Migrations';
        $dir_iterator = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            // 忽略目录和非php文件
            if (is_dir($file) || $file->getExtension() != 'php') {
                continue;
            }
            $file_path = str_replace('\\', '/',$file->getPathname());
            $className = str_replace('/', '\\',substr(substr($file_path, strlen($path)), 0, -4));
            $class_name = '\\Shopwwi\\Admin\\Install\\Migrations'. $className;
            if (!class_exists($class_name)) {
                echo "Class $class_name not found, skip route for it\n";
                continue;
            }
            $first = Db::connection()->table('migrations')->where('name',$className)->first();
            if($first != null) continue;
            $class = new $class_name;
            $class->up();
            Db::connection()->table('migrations')->insert(['name'=>$className,'created_at'=>now()]);
        }
    }

    /**
     * 删除数据表
     */
    public static function DropTable()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('migrations');
        $path = shopwwiAdminPath(). DIRECTORY_SEPARATOR .'Install'. DIRECTORY_SEPARATOR .'Migrations';
        $dir_iterator = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            // 忽略目录和非php文件
            if (is_dir($file) || $file->getExtension() != 'php') {
                continue;
            }
            $file_path = str_replace('\\', '/',$file->getPathname());
            $className = str_replace('/', '\\',substr(substr($file_path, strlen($path)), 0, -4));
            $class_name = '\\Shopwwi\\Admin\\Install\\Migrations'. $className;
            if (!class_exists($class_name)) {
                echo "Class $class_name not found, skip route for it\n";
                continue;
            }
            $class = new $class_name;
            $class->down();
        }
    }

    /**
     * 填充数据
     */
    public static function Seeders()
    {
        $path = shopwwiAdminPath(). DIRECTORY_SEPARATOR .'Install'. DIRECTORY_SEPARATOR .'Seeders';
        $dir_iterator = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            // 忽略目录和非php文件
            if (is_dir($file) || $file->getExtension() != 'php') {
                continue;
            }
            $file_path = str_replace('\\', '/',$file->getPathname());
            $className = str_replace('/', '\\',substr(substr($file_path, strlen($path)), 0, -4));
            $class_name = '\\Shopwwi\\Admin\\Install\\Seeders'. $className;
            if (!class_exists($class_name)) {
                echo "Class $class_name not found, skip route for it\n";
                continue;
            }
            $class = new $class_name;
            $class->run();
        }
    }

    public static function createMigrationsTable()
    {
        Db::connection()->getSchemaBuilder()->create('migrations', function (Blueprint $table) {
            $table->bigInteger('id', true)->comment('编号');
            $table->string('name', 200)->nullable()->default('')->comment('文件名称');
            $table->string('migration')->nullable()->default('')->comment('文件名称');
            $table->integer('batch')->nullable();
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->comment('数据表写入操作');
        });
    }
}