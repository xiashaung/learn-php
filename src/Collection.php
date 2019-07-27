<?php
namespace Xiashuang\Support;
/**
 * 基础数组筛选函数
 *
 * Class Collection
 */
class Collection  implements \Countable,\ArrayAccess,\Iterator
{
    private  $items = [];

    private $operates = [
        '=','==','>=','<=','<','>','in','!='
    ];

    /**
     * Collection constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * 取出数组中的某一列
     *
     * @param $column string 数组的某一列
     * @param $indexKey string 新数组的key,可以使用数组中某一列作为新数组的key
     * @return Collection
     */
    public function column($column,$indexKey = '')
    {
        return new static(array_column($this->items,$column, $indexKey));
    }

    /**
     * 取数组中的第个值
     *
     * @return Collection
     */
    public function first()
    {
        $first = array_shift($this->items) ?: [];
        return new static($first);
    }

    /**
     * @param $key  string 查找的key值
     * @param $operate string|mixed 操作或者key
     * @param null|mixed $value
     * @param bool $newIndex 是否需要重置数组的索引
     * @return static
     */
    public function where($key,$operate,$value = null,$newIndex = false)
    {
        if (is_null($value)){
            $value = $operate;
            $operate = '=';
        }

        $data = $this->filter($key,$operate,$value);

        if ($newIndex){
            $data = array_values($data);
        }
        
        return new static($data);
    }



    public function filter($key,$operate,$value)
    {
        return array_filter($this->items,$this->filterCallback($key,$operate,$value),ARRAY_FILTER_USE_BOTH);
    }

    private function filterCallback($key,$operate,$value)
    {
        return function($k,$v)use($key,$operate,$value){
             $realValue = $v[$key];
             switch ($operate){
                 case '=':
                 case '==':
                     return $realValue == $value;
                     break;
                 case '===':
                     return $realValue === $value;
                     break;
                 case '>=':
                     return $realValue >= $value;
                     break;
                 case '>':
                     return $realValue > $value;
                     break;
                 case '<=':
                     return $realValue <= $value;
                     break;
                 case '<':
                     return $realValue < $value;
                     break;
                 case 'in':
                     return in_array($realValue,$value);
                     break;
             }
        };
    }


    /**
     * 获取所有的数组
     *
     * @return Collection
     */
    public function all()
    {
       return new static($this->items); 
    }

    public function get()
    {
        return $this->all();
    }

    /**
     * @param $key string 需要排序的列
     * @param bool $desc 正序还是倒序
     * @param int $flag @see https://www.php.net/manual/zh/function.sort.php
     * @return $this|Collection
     */
    public function orderBy($key,$desc = false,$flag = SORT_REGULAR)
    {
        if (!$key){
            return $this;
        }
        $sort = [];
        //首先吧所有数组里的key赋值到一个新数组里
        foreach ($this->items as $k => $item){
            $sort[$k] =  $item[$key];
        }
        //使用内置函数进行排序
        $desc ? arsort($sort,$flag):asort($sort,$flag);
        //然后重新排列数组
        foreach (array_keys($sort) as $k){
            $sort[$key] = $this->items[$k];
        }
        return new static($sort);
    }


    /**
     * 倒序排列
     *
     * @param $key
     * @param int $flag
     * @return Collection
     */
    public function orderByDesc($key,$flag = SORT_REGULAR)
    {
        return $this->orderBy($key,true,$flag);
    }

    /**
     * 正序排列
     *
     * @param $key
     * @param int $flag
     * @return Collection
     */
    public function orderByAsc($key,$flag = SORT_REGULAR)
    {
        return $this->orderBy($key, false, $flag);
    }

    /**
     * use array_map
     *
     * @param \Closure $callback
     * @return array
     */
    public function map(\Closure $callback)
    {
        return array_map($callback, $this->items);
    }

    /**
     * use array_chunk
     *
     * @param $chunk
     * @return  static
     */
    public function chunk($chunk)
    {
        $arr = [];
        foreach (array_chunk($this->items, $chunk) as $value) {
            $arr[] = $value;
        }
        return new static($arr);
    }

    /**
     * 求某一列的平均值
     *
     * @param string $key
     * @return float|int
     */
    public function avg($key = '')
    {
        $arr = $key ? $this->column($key)->toArray():$this->items;
        return  array_sum($arr) / count($arr);
    }

    /**
     * 求总和
     *
     * @param string $key
     * @return float|int
     */
    public function sum($key = '')
    {
        $arr = $key ? $this->column($key)->toArray() : $this->items;
        return array_sum($arr);
    }


    /**
     * 转换为数组
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * 获取数组的长度
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @param $name
     * @return mixed|void
     */
    public function __get($name)
    {
       return $this[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this[$name] = $value;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
       return isset($this->items[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|void
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return mixed|void
     */
    public function offsetSet($offset, $value)
    {
        return $this->items[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
         unset($this->items[$offset]);
    }

    public function current()
    {
       return $this[$this->key];
    }

    public function next()
    {
       $this->key++;
    }

    public function key()
    {
       return $this->key;
    }

    public function valid()
    {
        return isset($this[$this->key]);
    }

    public function rewind()
    {
       $this->key = 0;
    }


}
