<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscribtion;
use Illuminate\Http\Request;
use NunoMaduro\Collision\Adapters\Phpunit\Subscribers\Subscriber;

class SubscriptionController extends Controller
{


    public function getSubscribers(){

        $subscriberss = Subscribtion::with('brocker')
        ->whereDate('end_date', '<', now())
        ->get();

        Subscribtion::whereDate('end_date', '<', now())->delete();



        $subscribers = Subscribtion::with('brocker')
        ->get();
        $data = $subscribers->map(function ($subscriber) {
            return [
                'sub_id'=>$subscriber->id,
                'brocker_id'=>$subscriber->brocker_id,
                'brocker_first_name'=>$subscriber->brocker->user->first_name,
                'brocker_last_name'=>$subscriber->brocker->user->last_name,
                'brocker_email'=>$subscriber->brocker->user->email,
                'brocker_phone'=>$subscriber->brocker->user->phone,
                'brocker_profit'=>$subscriber->brocker->profit,
                'brocker_leads'=>$subscriber->brocker->number_of_deals,
                'brocker_deals_done'=>$subscriber->brocker->deals_done,
                'brocker_experience'=>$subscriber->brocker->user->experience_year,
                'brocker_governate'=>$subscriber->brocker->user->governce,
                'plan_id'=>$subscriber->brocker->plan_id,
                'plan_name'=>$subscriber->brocker->plan->name,
                'subs_end_date'=>$subscriber->end_date,
                'plan_price'=>$subscriber->brocker->plan->price_after_discount
            ];
        });
        return response()->json(['subscribers'=>$data]);
    }

    public function deleteSubscribers($id){
        $subscriber = Subscribtion::find($id);
        $subscriber->delete();
        return response()->json(['message'=>'Subscriber Deleted Successfully']);
    }
}
