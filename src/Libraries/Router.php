<?php

namespace Shopwwi\Admin\Libraries;

use ReflectionClass;
use ReflectionMethod;
use Webman\Route;

class Router
{
    public static function resourcePath($prefix, $prefixPath, $plugin = null)
    {

        // 已经设置过路由的uri则忽略
        $routes = Route::getRoutes();
        $ignore_list = [];
        foreach ($routes as $tmp_route) {
            $ignore_list[$tmp_route->getPath()] = 0;
        }
        $hasRoute = function ($path, $method) {
            $routes = Route::getRoutes();
            foreach ($routes as $tmp_route) {
                if ($tmp_route->getPath() === $path && in_array($method, $tmp_route->getMethods())) {
                    return true;
                }
            }
            return false;
        };
        $suffix = 'Controller';
        $suffix_length = strlen($suffix);
        $path = shopwwiAdminPath() . DIRECTORY_SEPARATOR . 'App' . $prefixPath;
        if(!empty($plugin)) $path = base_path('plugin/' . $plugin . '/app'. $prefixPath);
        $dir_iterator = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($dir_iterator);

        foreach ($iterator as $file) {

            // 忽略目录和非php文件
            if (is_dir($file) || $file->getExtension() != 'php') {
                continue;
            }
            $file_path = str_replace('\\', '/', $file->getPathname());
            // 文件路径里不带controller的文件忽略
            if (strpos(strtolower($file_path), '/controllers/') === false && strpos(strtolower($file_path), '/Controllers/') === false) {
                continue;
            }

            // 只处理带 controller_suffix 后缀的
            if ($suffix_length && substr($file->getBaseName('.php'), -$suffix_length) !== $suffix) {
                continue;
            }

            // 根据文件路径计算uri
            $uri_path = str_replace(['/controllers/', '/Controllers/'], '/', substr(substr($file_path, strlen($path)), 0, -(4 + $suffix_length)));

            // 根据文件路径是被类名
            $class_name = '\\Shopwwi\\Admin\\App' . str_replace('/', '\\', $prefixPath . substr(substr($file_path, strlen($path)), 0, -4));
            if(!empty($plugin)) $class_name = '\\plugin\\' . $plugin . '\\app' . str_replace('/', '\\',$prefixPath . substr(substr($file_path, strlen($path)), 0, -4));

            if (!class_exists($class_name)) {
                echo "Class $class_name not found, skip route for it\n";
                continue;
            }

            $class = new ReflectionClass($class_name);
            $class_name = $class->name;
            $properties = $class->getDefaultProperties();
            $routePath = $properties['routePath'] ?? '';
            $routeAction = $properties['routeAction'] ?? [];
            $routeNoAction = $properties['routeNoAction'] ?? [];

            $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
            $AddRouter = function ($type, $uri, $cb) {
                // 同时设置多种请求类型的路由
                Route::add($type, $uri, $cb);

                $lower_uri = strtolower($uri);
                if ($lower_uri !== $uri) {
                    Route::add($type, $lower_uri, $cb);
                }
            };
            $route = function ($uri, $cb) use ($AddRouter, $routeAction, $ignore_list) {
                if (isset($ignore_list[strtolower($uri)])) {
                    return;
                }
                if (isset($routeAction[$cb[1]])) {

                    // 同时设置多种请求类型的路由
                    $AddRouter($routeAction[$cb[1]], $uri, $cb);
                } else {
                    Route::any($uri, $cb);
                    $lower_uri = strtolower($uri);
                    if ($lower_uri !== $uri) {
                        Route::any($lower_uri, $cb);
                    }
                }
            };
            // 设置路由
            if (empty($routePath)) {
                $routePaths = $prefix . $uri_path;
            } else {
                $ttr = explode("/", $uri_path);
                if($routePath == 'index'){
                    unset($ttr[count($ttr) - 1]);
                }else{
                    $ttr[count($ttr) - 1] = $routePath;
                }

                $frg = implode("/", $ttr);
                $routePaths = $prefix . $frg;
            }

            $methodsArray = collect($methods)->pluck('name')->toArray();
            $diff_options = \array_diff($methodsArray, ['index', 'create', 'store', 'update', 'show', 'edit', 'destroy', 'recovery', 'restore','erasure', '__construct', '__destruct']);
            if (!empty($diff_options)) {
                foreach ($diff_options as $action) {
                    if (in_array($action,$routeNoAction)) {
                        continue;
                    }
                    $route($routePaths . '/' . $action . '[/{id}]', [$class_name, $action]);
                }
            }

            // 注册路由 由于顺序不同会导致路由无效 因此不适用循环注册
            if (\in_array('index', $methodsArray) && !in_array('index',$routeNoAction)) $AddRouter(['GET'], $routePaths, [$class_name, 'index']);
            if (\in_array('create', $methodsArray) && !in_array('create',$routeNoAction)) $AddRouter(['GET', 'OPTIONS'], $routePaths . '/create', [$class_name, 'create']);
            if (\in_array('store', $methodsArray) && !in_array('store',$routeNoAction)) $AddRouter(['POST'], $routePaths, [$class_name, 'store']);
            if (\in_array('update', $methodsArray) && !in_array('update',$routeNoAction)) $AddRouter(['PUT'], $routePaths . '/{id}', [$class_name, 'update']);
            if (\in_array('recovery', $methodsArray) && !in_array('recovery',$routeNoAction)) $AddRouter(['GET', 'OPTIONS'], $routePaths . '/recovery', [$class_name, 'recovery']);
            if (\in_array('show', $methodsArray) && !in_array('show',$routeNoAction)) $AddRouter(['GET'], $routePaths . '/{id}', [$class_name, 'show']);
            if (\in_array('edit', $methodsArray) && !in_array('edit',$routeNoAction)) $AddRouter(['GET', 'OPTIONS'], $routePaths . '/{id}/edit', [$class_name, 'edit']);
            if (\in_array('destroy', $methodsArray) && !in_array('destroy',$routeNoAction)) $AddRouter(['DELETE'], $routePaths . '/{id}', [$class_name, 'destroy']);
            if (\in_array('restore', $methodsArray) && !in_array('restore',$routeNoAction)) $AddRouter(['PUT'], $routePaths . '/recovery/{id}', [$class_name, 'restore']);
            if (\in_array('erasure', $methodsArray) && !in_array('erasure',$routeNoAction)) $AddRouter(['DELETE'], $routePaths . '/recovery/{id}', [$class_name, 'erasure']);
            // 注册OPTIONS路由防止跨域无效

            if (!empty(array_intersect($methodsArray, ['index', 'store']))) {
                Route::options($routePaths, function () {
                    return response('不要淘气哦！路由不存在啦');
                });
                $lower_uri = strtolower($routePaths);
                if ($lower_uri !== $routePaths) {
                    Route::options($lower_uri, function () {
                        return response('不要淘气哦！路由不存在啦');
                    });
                }
            }
            if (!empty(array_intersect($methodsArray, ['update', 'show', 'destroy']))) {
                Route::options($routePaths . '/{id}', function () {
                    return response('不要淘气哦！路由不存在啦');
                });
                $lower_uri = strtolower($routePaths . '/{id}');
                if ($lower_uri !== $routePaths . '/{id}') {
                    Route::options($lower_uri, function () {
                        return response('不要淘气哦！路由不存在啦');
                    });
                }
            }
            if (!empty(array_intersect($methodsArray, ['restore', 'erasure']))) {
                Route::options($routePaths . '/recovery/{id}', function () {
                    return response('不要淘气哦！路由不存在啦');
                });
                $lower_uri = strtolower($routePaths . '/recovery/{id}');
                if ($lower_uri !== $routePaths . '/recovery/{id}') {
                    Route::options($lower_uri, function () {
                        return response('不要淘气哦！路由不存在啦');
                    });
                }
            }
        }
    }

}