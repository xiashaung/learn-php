<?php

/**
 * 采用异或来找出数组中唯一没有重复的值
 *
 * @param array $data 数组中的值只能为整数
 * @return array
 */
function find_not_repeat(array $data = [])
{
    //寻找出数组中单个值,且值只有一个  异或具有交换律和结合律
    $temp = $data[0];
    for ($i = 1; $i < count($data); $i++) {
        $temp = $temp ^ $data[$i];
    }
    return $temp;
}


