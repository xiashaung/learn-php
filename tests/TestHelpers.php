<?php
require  'BaseTests.php';

class TestHelpers  extends  BaseTests
{
    /**
     * 找出数组中没有重复的值
     */
    public function testFindNotRepeat()
    {
        $data = [7,8,8,10,10,11,12,11,12];
        self::assertEquals(find_not_repeat($data),7);
    }
}