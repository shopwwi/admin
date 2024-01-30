<?php

namespace Shopwwi\Admin\Libraries;
use Illuminate\Translation;
use Illuminate\Validation\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\Translator;
use support\Db;

class ValidatorFactory
{
    /**
     * @var Factory
     */
    private $factory;

    public function __construct()
    {
        $this->factory = new Factory($this->loadTranslator());
        $this->factory->extend('chs', function($attribute, $value, $parameters){
            return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $value);
        });
        $this->factory->extend('chs_alpha', function($attribute, $value, $parameters){
            return preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u', $value);
        });
        $this->factory->extend('chs_alpha_num', function($attribute, $value, $parameters){
            return preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', $value);
        });
        $this->factory->extend('chs_dash', function($attribute, $value, $parameters){
            return preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\_\-]+$/u', $value);
        });
        $this->factory->extend('chs_dash_pwd', function($attribute, $value, $parameters){
            return preg_match('/^[a-zA-Z0-9!@#$%^&*]+$/u', $value);
        });
        $this->factory->extend('chs_unique', function($attribute, $value, $parameters){
            $info = Db::table($parameters[0])->where($attribute,$value)->first();
            if($info == null){
                return true;
            }else{
                return false;
            }
        });
        $this->factory->extend('chs_as_unique', function($attribute, $value, $parameters){
            $info = Db::table($parameters[0])->where($attribute,$value)->where($parameters[2] ?? 'id','!=',$parameters[1])->first();
            if($info == null){
                return true;
            }else{
                return false;
            }
        });
    }

    protected function loadTranslator(): Translator
    {
        $langPath = config('translation.path');

        $filesystem = new Filesystem();
        $loader = new Translation\FileLoader($filesystem, $langPath);
        $loader->addNamespace('lang', $langPath);
        $loader->load(config('translation.locale'), 'validation', 'lang');
        return new Translator($loader, config('translation.locale'));
    }

    public function __call(string $method, array $args)
    {
        return call_user_func_array([$this->factory, $method], $args);
    }
}
