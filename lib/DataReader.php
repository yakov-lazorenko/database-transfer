<?php

interface DataReader
{
    public function init($settings);
    public function run();
}