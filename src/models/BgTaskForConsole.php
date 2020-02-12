<?php


namespace dsj\bgtask\models;


class BgTaskForConsole extends BgTask
{
    public function beforeSave($insert)
    {
        return true;
    }
}