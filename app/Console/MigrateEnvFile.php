<?php
/**
 * 迁移 .env 文件
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/11/3
 * @time 15:57
 */

namespace Luolongfei\App\Console;

use Luolongfei\Libs\Env;

class MigrateEnvFile extends Base
{
    /**
     * @var array 当前已有的环境变量数据
     */
    protected $allOldEnvValues;

    /**
     * @var int 迁移环境变量数量
     */
    public $migrateNum = 0;

    /**
     * @var MigrateEnvFile
     */
    private static $instance;

    /**
     * @return MigrateEnvFile
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->allOldEnvValues = $this->getAllOldEnvValues();
    }

    private function __clone()
    {
    }

    /**
     * 获取当前有效的旧的环境变量值
     *
     * 会做一些基本的处理，让旧版数据兼容新版 .env 文件
     *
     * @return array
     */
    protected function getAllOldEnvValues()
    {
        $allOldEnvValues = env();

        unset($allOldEnvValues['ENV_FILE_VERSION']);

        $allOldEnvValues = array_filter($allOldEnvValues, function ($val) {
            return $val !== '';
        });

        $allOldEnvValues = array_map(function ($val) {
            $tmpVal = strtolower($val);

            if ($tmpVal === 'true' || $tmpVal === true) {
                return 1;
            } else if ($tmpVal === 'false' || $tmpVal === false) {
                return 0;
            } else {
                return $val;
            }
        }, $allOldEnvValues);

        return $allOldEnvValues;
    }

    /**
     * 是否需要迁移
     *
     * @return bool
     * @throws \Exception
     */
    public function needToMigrate()
    {
        $envVer = $this->getEnvFileVer();

        if (is_null($envVer)) {
            return true;
        }

        $envExampleVer = $this->getEnvFileVer('.env.example');

        return version_compare($envExampleVer, $envVer, '>');
    }

    public function getEnvFilePath($filename = '.env')
    {
        return ROOT_PATH . DS . $filename;
    }

    /**
     * 获取 env 文件版本
     *
     * @param string $filename
     *
     * @return string|null
     * @throws \Exception
     */
    public function getEnvFileVer($filename = '.env')
    {
        $file = $this->getEnvFilePath($filename);

        if (!file_exists($file)) {
            throw new \Exception(lang('100021') . $file);
        }

        if (($fileContent = file_get_contents($file)) === false) {
            throw new \Exception(lang('100022') . $file);
        }

        if (!preg_match('/^ENV_FILE_VERSION=(?P<env_file_version>.*?)$/im', $fileContent, $m)) {
            return null;
        }

        return $this->getVerNum($m['env_file_version']);
    }

    /**
     * 备份旧文件
     *
     * 如果目标文件已存在，将会被覆盖
     *
     * @return bool
     * @throws \Exception
     */
    public function backup()
    {
        if (copy($this->getEnvFilePath(), $this->getEnvFilePath('.env.old')) === false) {
            throw new \Exception(lang('100020'));
        }

        return true;
    }

    /**
     * 生成新的 .env 文件
     *
     * @return bool
     * @throws \Exception
     */
    public function genNewEnvFile()
    {
        if (copy($this->getEnvFilePath('.env.example'), $this->getEnvFilePath('.env')) === false) {
            throw new \Exception(lang('100019'));
        }

        return true;
    }

    /**
     * 迁移环境变量数据
     *
     * @param array $allEnvVars
     * @throws \Exception
     */
    public function migrateData(array $allEnvVars)
    {
        foreach ($allEnvVars as $envKey => $envVal) {
            if ($this->setEnv($envKey, $envVal)) {
                $this->migrateNum++;
            }
        }

        // 重载环境变量
        Env::getInstance()->init('.env', true);
    }

    /**
     * 写入单个环境变量值
     *
     * @param string $key
     * @param $value
     *
     * @return bool
     */
    public function setEnv(string $key, $value)
    {
        $envFilePath = $this->getEnvFilePath();
        $contents = file_get_contents($envFilePath);

        $contents = preg_replace("/^{$key}=[^\r\n]*/miu", $this->formatEnvVal($key, $value), $contents, -1, $count);

        return $this->writeFile($envFilePath, $contents) && $count;
    }

    /**
     * 格式化环境变量
     *
     * @param string $key
     * @param string|integer $value
     *
     * @return string
     */
    public function formatEnvVal($key, $value)
    {
        return sprintf(is_numeric($value) ? '%s=%s' : "%s='%s'", $key, $value);
    }

    /**
     * 覆盖文件内容
     *
     * @param string $path
     * @param string $contents
     *
     * @return bool
     */
    protected function writeFile(string $path, string $contents): bool
    {
        $file = fopen($path, 'w');
        fwrite($file, $contents);

        return fclose($file);
    }

    /**
     * @return bool
     */
    public function handle()
    {
        try {
            if (!$this->needToMigrate()) {
                return true;
            }

            system_log(lang('100013'));

            $this->backup();
            system_log(sprintf(lang('100014'), ROOT_PATH));

            $this->genNewEnvFile();
            system_log(lang('100015'));

            $this->migrateData($this->allOldEnvValues);
            system_log(sprintf(lang('100016'), $this->migrateNum));

            system_log(lang('100017'));

            return true;
        } catch (\Exception $e) {
            system_log(lang('100018') . $e->getMessage());

            return false;
        }
    }
}
