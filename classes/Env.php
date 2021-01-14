<?php

namespace main;

use Dotenv\Dotenv;
use Dotenv\Loader;

class Env
{
    const dotFile = '.env.php';

    public static function get($key, $default = null)
    {
        $loader = new Loader(__DIR__, true);
        $value = $loader->getEnvironmentVariable($key);

        if ($value === null) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;
        }

        return $value;
    }

    public static function load($path, $required = [])
    {
        $dotenv = new Dotenv($path);
        if (!self::loadCache($path . '/' . self::dotFile)) {
            $dotenv->load();
        }
        $dotenv->required($required);
    }

    protected static function loadCache($file)
    {
        $list = @include_once($file);
        if ($list) {
            $loader = new Loader(null, true);
            foreach ($list as $name => $value) {
                $loader->setEnvironmentVariable($name, $value);
            }
            return true;
        }
        return false;
    }

    public static function saveCache($path)
    {
        $dotenv = new Dotenv($path);
        $dotenv->load();
        $list = $dotenv->getEnvironmentVariableNames();

        $result = ['<?php', '// cached at ' . date('Y-m-d H:i:s'), 'return ['];
        foreach ($list as $key) {
            $value = env($key);
            if (is_bool($value)) {
                $result[] = '    \'' . $key . '\' => \'' . ($value ? 'true' : 'false') . '\',';
            } elseif (is_numeric($value)) {
                $result[] = '    \'' . $key . '\' => ' . $value . ',';
            } else {
                $result[] = '    \'' . $key . '\' => \'' . $value . '\',';
            }
        }
        $result[] = '];';
        return file_put_contents($path . '/' . self::dotFile, implode("\n", $result));
    }

    public static function clearCache($path)
    {
        if (file_exists($path . '/' . self::dotFile)) {
            unlink($path . '/' . self::dotFile);
        }
    }
}