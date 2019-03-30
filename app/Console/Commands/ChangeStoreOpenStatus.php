<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/4
 * Time: 20:15
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChangeStoreOpenStatus extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change_store_open_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '这是一个测试Laravel定时任务的描述';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    public function handle(){
        \Log::info("肤若凝脂气幽兰");
    }
}