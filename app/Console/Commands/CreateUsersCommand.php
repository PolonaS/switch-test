<?php

namespace App\Console\Commands;

use App\Order;
use App\OrderItem;
use App\User;
use Illuminate\Console\Command;
use DB;
use League\Flysystem\Exception;

class CreateUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create 
        {--upl=10 : Users per level} 
        {--ul=3 : User levels} 
        {--opu=2 : Orders per user} 
        {--ipo=3 : Item per order}
        ';

    private $usersPerLevel;
    private $userLevels;
    private $ordersPerUser;
    private $itemPerOrder;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->usersPerLevel = $this->option("upl");
        $this->userLevels = $this->option("ul");
        $this->ordersPerUser = $this->option("opu");
        $this->itemPerOrder = $this->option("ipo");

        DB::beginTransaction();
        try {
            DB::raw("TRUNCATE order_items");
            DB::raw("TRUNCATE orders");
            DB::raw("TRUNCATE users");

            $this->createUsersInLevel(1, null);

            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
        }

        $this->info("Users: ". User::count());
        $this->info("Orders: ". Order::count());
    }

    private function createUsersInLevel(int $level, $parentId = null)
    {
        for($c=0; $c<$this->usersPerLevel; $c++){
            $user = $this->createUser($parentId);

            if($level < $this->userLevels){
                $this->createUsersInLevel(($level+1), $user->id);
            }
        }
    }

    private function createUser($parentId = null)
    {
        $user = factory(User::class)->create(["parent_id" => $parentId]);

        for($c=0; $c<$this->ordersPerUser; $c++){
            $this->createOrder($user->id);
        }

        return $user;
    }

    private function createOrder(int $userId)
    {
        $order = new Order();
        $order->user_id = $userId;
        $order->save();

        for ($c=0; $c<$this->itemPerOrder; $c++){
            factory(OrderItem::class)->create(["order_id" => $order->id]);
        }

        return $order;
    }
}
