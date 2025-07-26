<?php

namespace App\Helpers;

class TaskHelper
{
    /**
     * task_type_noを文字列に変換
     */
    public static function getTaskTypeName($taskTypeNo)
    {
        switch ($taskTypeNo) {
            case 0:
                return '直接';
            case 1:
                return '間接';
            case 2:
                return 'その他';
            default:
                return '';
        }
    }

    /**
     * task_category_noを文字列に変換
     */
    public static function getTaskCategoryName($taskCategoryNo)
    {
        switch ($taskCategoryNo) {
            case 0:
                return '肉体的負担';
            case 1:
                return '精神的負担';
            case 2:
                return 'その他';
            default:
                return '';
        }
    }
} 