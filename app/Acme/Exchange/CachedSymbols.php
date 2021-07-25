<?php


namespace App\Acme\Exchange;


use App\Acme\CarbonFa\CarbonFa;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CachedSymbols
{

    private $name;

    private $gregorian;

    public static function of($name)
    {
        return (new static())->setName($name);
    }

    /**
     * @param $func
     * @return array|bool|mixed
     */
    public function remember($func)
    {
        if($result = $this->exists()) return $result;

        return $this->save($func);
    }

    /**
     * @param $name
     * @param $func
     * @return array
     */
    public function save($func)
    {
        $folder = $this->getCacheFolder();
        $filename = $this->getCacheFileName();

        if(Storage::exists($path = "$folder/cache/"))
        {
            Storage::deleteDirectory($path);
        }

        Storage::makeDirectory($path, 0755, true);

        $result = $func instanceof Closure ? $func() : $func;

        $result = $result instanceof Collection ? $result->toArray() : $result;

        Storage::put($filePath = "$path/$filename",json_encode($result));

        return $result;
    }

    /**
     * @param $name
     * @return bool|mixed
     */
    public function exists()
    {
        $folder = $this->getCacheFolder();
        $filename = $this->getCacheFileName();

        if(!Storage::exists($path = "$folder/cache/$filename")) return false;

        return json_decode(Storage::get($path), true);
    }


    /**
     * @param $name
     * @return string
     */
    public function getCacheFolder()
    {
        return Str::snake(class_basename($this->name));
    }

    /**
     * @return string
     */
    public function getCacheFileName()
    {
        if($this->gregorian)
            return Carbon::now()->format("Y_m_d");

        return CarbonFa::now(new \DateTimeZone('Asia/Tehran'))->format("Y_m_d");
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function gregorian()
    {
        $this->gregorian = true;
        return $this;
    }

}
