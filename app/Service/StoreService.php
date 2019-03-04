<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 4:28
 */

namespace App\Service;

use DB;

class StoreService{

    // 获取推荐店家列表
    public function getRecommendedStoreList($size = 0, $keyword = "", $address = null){
        $field = sprintf('get_distance(lat,lng,%s,%s) as distance', $address['lat'], $address['lng']);
        $builder = DB::table('store')
            ->where('status', 1)
            ->where('is_recommended', 1);

        if ($keyword != ""){
            $like_str = sprintf("%%%s%%", $keyword);
            $builder->where('name', 'like', $like_str);
        }
        $builder->selectRaw($field)->addSelect('store.*');

        if ($size != 0){
            $list = pageSelect($builder, $size);
        }else{
            $list = pageSelect($builder, env('API_PAGE_SIZE', 20));
        }

        $this->renderStore($list['data']);
        return $list;
    }

    // 获取热门店家列表
    public function getHotStoreList($size = 0, $keyword = "", $address = null){
        $field = sprintf('get_distance(lat,lng,%s,%s) as distance', $address['lat'], $address['lng']);
        $builder = DB::table('store')
            ->where('is_hot', 1)
            ->where('status', 1)
            ->orderBy('total_sale', 'desc');

        if ($keyword != ""){
            $like_str = sprintf("%%%s%%", $keyword);
            $builder->where('name', 'like', $like_str);
        }

        $builder->selectRaw($field)->addSelect('store.*');

        if ($size != 0){
            $list = pageSelect($builder, $size);
        }else{
            $list = pageSelect($builder, env('API_PAGE_SIZE', 20));
        }

        $this->renderStore($list['data']);
        return $list;
    }

    public function renderStore(&$list = []){
        foreach ($list as $k => $v){
            // 配送费
            $v['delivery_fee'] = intval(calculate_delivery_fee($v['distance']));
            // 是否超出配送距离
            $check_result = $this->checkStoreAbnormalStatus($v);
            $v['abnormal_status'] = $check_result['status'];
            $v['tip_business_time'] = $check_result['str'];
            $list[$k] = $v;
        }
    }

    public function checkStoreAbnormalStatus($store){
        // 0 - 正常， 1 - 超出配送范围， 2 - 不在营业时间
        $return['status'] =  $store['distance'] > $store['delivery_range'] ? 1 : 0;
        $return['str'] = "";

        // 若是已超出配送范围，直接返回异常
        if($return['status'] == 1){
            return $return;
        }

        $return = $this->checkStoreBusinessTime($store['business_time']);

        return $return;
    }

    public function checkStoreBusinessTime($business_time){
        $now = timeStrToTime(date("H:i"));
        $business_time_list = explode("、", $business_time);
        $return['status'] = 2;
        $return['str'] = "";

        $min_time = 1440;
        $max_time_range = "";
        $max_time = 0;
        $min_time_range = "";
        // 异常状态
        foreach ($business_time_list as $k => $v){
            $store_business_time['begin_time'] = explode("-", $v)[0];
            $store_business_time['end_time'] = explode("-", $v)[1];

            if (timeStrToTime($store_business_time['begin_time']) > $max_time){
                $max_time = timeStrToTime($store_business_time['begin_time']);
                $max_time_range = $v;
            }
            if (timeStrToTime($store_business_time['begin_time']) < $min_time){
                $min_time = timeStrToTime($store_business_time['begin_time']);
                $min_time_range = $v;
            }

            if (timeStrToTime($store_business_time['begin_time']) < $now && timeStrToTime($store_business_time['end_time']) > $now){
                $return['status'] = 0;
                break;
            }
            $return['status'] = 2;
        }
        // 店家下次的营业时间
        if ($return['status'] == 2){
            if ($now > $max_time){
                if ($business_time == "09:00-12:30、23:30-23:50"){

                }
                $return['str'] = "明天{$min_time_range}";
            }
            if ($now > $min_time && $now < $max_time){
                $return['str'] = "{$max_time_range}";
            }
            if ($now < $min_time){
                $return['str'] = "{$min_time_range}";
            }
        }

        return $return;
    }
}