<?php

namespace App\Console\Commands\Spider;

use Carbon\Carbon;
use App\Jobs\Haohuo;
use App\Jobs\SaveGoods;
use App\Jobs\SaveOrders;
use App\Jobs\Spider\DownItem;
use App\Models\Taoke\Setting;
use App\Jobs\Spider\KuaiQiang;
use App\Jobs\Spider\TalentInfo;
use Illuminate\Console\Command;
use App\Tools\Taoke\TBKInterface;

class Taobao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:tb {name? : The name of the spider} {--type=3} {--all=false} {--h=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '好单库爬虫';

    /**
     * @var
     */
    protected $tbk;

    /**
     * Taobao constructor.
     * @param TBKInterface $tbk
     */
    public function __construct(TBKInterface $tbk)
    {
        $this->tbk = $tbk;
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $name = $this->argument('name');
        switch ($name) {
            case 'haohuo':
                $this->haohuo();
                break;
            case 'danpin':
                $this->danpin();
                break;
            case 'zhuanti':
                $this->zhuanti();
                break;
            case 'kuaiqiang':
                $this->kuaiqiang();
                break;
            case 'timingItems':
                $this->timingItems();
                break;
            case 'updateCoupon':
                $this->updateCoupon();
                break;
            case 'deleteCoupon':
                $this->deleteCoupon();
                break;
            case 'order':
                $this->getOrders();
                break;
            case 'updateOrder':
                $this->updateOrder();
                break;
            case 'talent':
                $this->talentInfo();
                break;
            default:
                $this->all();
                break;
        }
    }

    /**
     * 全网优惠券.
     */
    protected function all()
    {
        //数据类型
        $type = $this->option('type');
        //是否爬取所有
        $all = $this->option('all');

        $this->info('正在爬取好单库优惠券');
        //开始爬取
        try {
            $totalPage = 6000;
            if ($all == 'false') {
                $totalPage = 20;
            }

            $this->info("总页码:{$totalPage}");
            $bar = $this->output->createProgressBar($totalPage);

            $min_id = 1;
            for ($i = 1; $i <= $totalPage; $i++) {
                $response = $this->tbk->spider([
                    'type' => $type,
                    'min_id' => $min_id,
                ]);

                if ($response) {
                    SaveGoods::dispatch($response['data'], 'taobao', $type, $all);
                }
                $min_id = $response['min_id'];
                $bar->advance();
                $this->info(" >>>已采集完第{$i}页");
            }
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 好货专场.
     * @throws \Exception
     */
    protected function haohuo()
    {
        // 好货专场
        try {
            $this->info('正在爬取好货专场');
            $totalPage = 1000;
            $bar = $this->output->createProgressBar($totalPage);
            $min_id = 1;
            for ($i = 1; $i < $totalPage; $i++) {
                $this->info($min_id);
                $result = $this->tbk->haohuo(['min_id' => $min_id]);
                // 队列
                if ($result->min_id != $min_id) {
                    Haohuo::dispatch($result->data);
                    $min_id = $result->min_id;
                    $this->info($min_id);
                    $bar->advance();
                    $this->info(">>>已采集完第{$i}页 ");
                }
            }
            $bar->finish();
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 精选单品
     */
    protected function danpin()
    {
        try {
            $total = 50;
            $this->info('正在爬取精选单品！');
            $bar = $this->output->createProgressBar($total);
            $min_id = 1;
            for ($i = 1; $i <= $total; $i++) {
                $resp = $this->tbk->danpin(['min_id' => $min_id]);
                if ($min_id != $resp['min_id']) {
                    // 队列
                    \App\Jobs\Spider\JingXuan::dispatch($resp['data']);
                    $min_id = $resp['min_id'];
                    $bar->advance();
                    $this->info(">>>已采集完第{$total}页 ");
                }
            }
            $bar->finish();
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 精选专题.
     */
    protected function zhuanti()
    {
        $res = $this->tbk->zhuanti();
        try {
            foreach ($res->data as $re) {
                $items = $this->tbk->zhuantiItem([
                    'id' => $re->id,
                ]);
                $data = [];
                foreach ($items->data as $k => $v) {
                    $data[$k]['title'] = $v->itemtitle; //标题
                    $data[$k]['short_title'] = $v->itemshorttitle; //短标题
                    $data[$k]['itemid'] = $v->itemid;
                    $data[$k]['price'] = $v->itemprice; //在售价
                    $data[$k]['coupon_price'] = $v->itemendprice; //卷后价
                    $data[$k]['pic_url'] = $v->itempic; //图片
                    $data[$k]['type'] = 1;
                }
                $insert = [
                    'special_id' => $re->id,
                    'title'      => $re->name,
                    'thumb'      => 'http://img.haodanku.com/'.$re->app_image,
                    'banner'     => 'http://img.haodanku.com/'.$re->image,
                    'content'    => $re->content,
                    'items'      => json_encode($data),
                    'start_time' => date('Y-m-d H:i:s', $re->activity_start_time),
                    'end_time'   => date('Y-m-d H:i:s', $re->activity_end_time),
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
                db('tbk_zhuanti')->updateOrInsert([
                    'title' => $re->name,
                ], $insert);
            }
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 快抢商品
     */
    protected function kuaiQiang()
    {
        try {
            $total = 5;
            $bar = $this->output->createProgressBar($total * 15);
            $min_id = 1;
            for ($j = 1; $j <= 15; $j++) {
                for ($i = 1; $i <= $total; $i++) {
                    try {
                        $res = $this->tbk->kuaiQiang(['min_id' => $min_id, 'hour_type' => $j]);
                    } catch (\Exception $e) {
                        continue;
                    }
//                    $this->info($j);
                    // 队列
                    KuaiQiang::dispatch($res['data'], $j);
                    $min_id = $res['min_id'];
                    $bar->advance();
                    $this->info('>>>已采集完第'.$i * $j.'页 ');
                }
                $min_id = 1;
            }
            $bar->finish();
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 定时拉取.
     */
    protected function timingItems()
    {
        try {
            $totalPage = 50;
            $bar = $this->output->createProgressBar($totalPage);
            $min_id = 1;
            for ($i = 1; $i < $totalPage; $i++) {
                $this->info($min_id);
                $results = $this->tbk->timingItems(['min_id' => $min_id]);
                if ($results['min_id'] != $min_id) {
                    //队列
                    SaveGoods::dispatch($results['data'], 'timingItems');
                    $min_id = $results['min_id'];
                    $bar->advance();
                    $this->info(">>>已采集完第{$i}页 ");
                }
            }
            $bar->finish();
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 商品更新.
     */
    protected function updateCoupon()
    {
        try {
            $total = 50;
            $bar = $this->output->createProgressBar($total);
            $min_id = 1;
            for ($i = 1; $i <= $total; $i++) {
                $res = $this->tbk->updateCoupon(['min_id' => $min_id]);
                if ($min_id != $res['min_id']) {
                    // 队列
                    \App\Jobs\Spider\UpdateItem::dispatch($res['data']);
                    $min_id = $res['min_id'];
                    $bar->advance();
                    $this->info(">>>已采集完第{$min_id}页 ");
                }
            }
            $bar->finish();
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 失效商品
     */
    protected function deleteCoupon()
    {
        try {
            $end = date('H');
            if ($end == 0) {
                $end = 23;
            }
            $start = $end - 1;
            $rest = $this->tbk->deleteCoupon([
                'start' => $start,
                'end' => $end,
            ]);
            // 队列
            DownItem::dispatch($rest);
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 获取订单.
     */
    protected function getOrders()
    {
        try {
            $settings = Setting::query()->get();
            $bar = $this->output->createProgressBar(10 * $settings->count());
            $type = $this->option('type');
            foreach ($settings as $setting) {
                //循环所有页码查出数据

                for ($page = 1; $page <= 4; $page++) {
                    $resp = $this->tbk->getOrders([
                        'page' => $page,
                        'setting' => $setting,
                        'type' => $type,
                    ]);

                    //写入队列
                    SaveOrders::dispatch($resp, 'taobao');
                    $bar->advance();
                    $this->info(">>>已采集完第{$page}页 ");
                }
            }
            $bar = $this->output->createProgressBar(4);

            $bar->finish();
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 更新订单.
     */
    protected function updateOrder()
    {
        try {
            $carbon = new Carbon();
            $settings = Setting::query()->get();
            $bar = $this->output->createProgressBar(10 * $settings->count());
            $type = $this->option('type');
            $orders = db('tbk_orders')->select(['created_at'])->where([
                'status' => 1,
                'type' => 1,
            ])->get();
            foreach ($settings as $setting) {
                //循环所有订单
                foreach ($orders as $order) {
                    try {
                        $resp = $this->tbk->getOrders([
                            'page' => 1,
                            'setting' => $setting,
                            'type' => $type,
                            'start_time' => $carbon->parse($order->created_at)->subMinute(5)->toDateTimeString(),
                        ]);
                    } catch (\Exception $e) {
                        $this->warn($e->getMessage());
                        continue;
                    }

                    //写入队列
                    SaveOrders::dispatch($resp, 'taobao');
                    $bar->advance();
                }
            }
            $bar = $this->output->createProgressBar(4);

            $bar->finish();
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * 达人说.
     */
    protected function talentInfo()
    {
        try {
            $this->info('正在采集达人说');
            $resp = $this->tbk->talentInfo();
            if (isset($resp->topdata)) {
                foreach ($resp->topdata as $topdatum) {
                    $rest = $this->tbk->talentArticle($topdatum->id);
                    $rest_arr[] = $rest;
                }
                TalentInfo::dispatch($rest_arr, 1);
            }
            if (isset($resp->newdata)) {
                foreach ($resp->newdata as $newdata) {
                    $new_data = $this->tbk->talentArticle($newdata->id);
                    $now_arr[] = $new_data;
                }
                TalentInfo::dispatch($now_arr, 2);
            }
            if (isset($resp->clickdata)) {
                foreach ($resp->clickdata as $clickdata) {
                    $click_data = $this->tbk->talentArticle($clickdata->id);
                    $click_arr[] = $click_data;
                }
                TalentInfo::dispatch($click_arr, 3);
            }
            $this->info('达人说采集结束');
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }
}
