<?php
/**
 * Created by PhpStorm.
 * User: wanda
 * Date: 2018/7/2
 * Time: 下午2:28
 */

namespace Jpusher;

use JPush\Client;
use App\Models\User;
use Illuminate\Support\Collection;

class JpusherClient
{
    private $Jpush;
    private $DebugJpush;
    private $Config;
    private $DebugConfig;

    public function __construct($config, $pushRecordMode)
    {
        if ($config['DEBUG_SWITCH']) {

            $this->DebugConfig = [
                'JPUSH_APPKEY'       => $config['DEBUG_JPUSH_APPKEY'],
                'JPUSH_MASTERSECRET' => $config['DEBUG_JPUSH_MASTERSECRET'],
            ];
            $this->DebugJpush  = new Client($this->DebugConfig['JPUSH_APPKEY'], $this->DebugConfig['JPUSH_MASTERSECRET']);
            $this->DebugJpush  = $this->DebugJpush->push();
            $this->DebugJpush->setPlatform('all');
        }
        $this->Config = [
            'JPUSH_APPKEY'       => $config['JPUSH_APPKEY'],
            'JPUSH_MASTERSECRET' => $config['JPUSH_MASTERSECRET'],
        ];
        $this->Jpush  = new Client($this->Config['JPUSH_APPKEY'], $this->Config['JPUSH_MASTERSECRET']);
        $this->Jpush  = $this->Jpush->push();
        $this->Jpush->setPlatform('all');

    }

    /**
     * @param $scanUser 扫码者
     * @param $qrcodeUser 二维码所有者
     */
    public function subscribeUserNotice(User $scanUser, User $qrcodeUser)
    {

        //配置安卓的测试环境的调试
        $string         = "同行" . $scanUser->nick_name . "通过扫码关注了您！";
        $apnsProduction = true;
        if (count($this->DebugConfig) > 0) {
            //发送给的用户
            $this->DebugJpush->addTag($qrcodeUser->id);
            //设置推送内容

            $this->DebugJpush->setNotificationAlert($string);
            //ios推送内容
            $this->DebugJpush->iosNotification($string, [
                'badge'  => '+1',
                'extras' => [
                    'object_id' => $scanUser->id,
                    'type'      => 'subscribe'
                ]
            ]);
            //安卓推送
            $this->DebugJpush->androidNotification($string, [
                'title'  => '关注通知',
                'extras' => [
                    'object_id' => $scanUser->id,
                    'type'      => 'subscribe'
                ]
            ]);
            //配置通知
            $apnsProduction = false;
            $this->DebugJpush->options(['apns_production' => $apnsProduction]);
        }
        //发送给的用户
        $this->Jpush = $this->addTag($qrcodeUser->id);
        //设置推送内容
        $this->Jpush->setNotificationAlert($string);
        //ios推送内容
        $this->Jpush->iosNotification($string, [
            'badge'  => '+1',
            'extras' => [
                'object_id' => $scanUser->id,
                'type'      => 'subscribe'
            ]
        ]);
        //安卓推送
        $this->Jpush->androidNotification($string, [
            'title'  => '关注通知',
            'extras' => [
                'object_id' => $scanUser->id,
                'type'      => 'subscribe'
            ]
        ]);
        //配置通知
        $this->Jpush->options(['apns_production' => $apnsProduction]);
        return $this->send();
    }


    /**
     * @param $user 上新的用户
     * @param $users 关注上新的用户
     */
    public function userUpGoodsNotice(User $user, Collection $users)
    {
        //配置安卓的测试环境的调试
        $string         = "您关注的同行“{$user->nick_name}”上新了商品！";
        $apnsProduction = true;
        $sendUserid     = array_map(function ($v) {
            return $v->id;
        }, $users);
        if (count($this->DebugConfig) > 0) {
            //发送给的用户
            $this->DebugJpush->addTag($sendUserid);
            //设置推送内容

            $this->DebugJpush->setNotificationAlert($string);
            //ios推送内容
            $this->DebugJpush->iosNotification($string, [
                'badge'  => '+1',
                'extras' => [
                    'object_id' => $user->id,
                    'type'      => 'upGoods'
                ]
            ]);
            //安卓推送
            $this->DebugJpush->androidNotification($string, [
                'title'  => '上新通知',
                'extras' => [
                    'object_id' => $user->id,
                    'type'      => 'upGoods'
                ]
            ]);
            //配置通知
            $apnsProduction = false;
            $this->DebugJpush->options(['apns_production' => $apnsProduction]);
        }
        //发送给的用户
        $this->Jpush = $this->addTag($sendUserid->id);
        //设置推送内容
        $this->Jpush->setNotificationAlert($string);
        //ios推送内容
        $this->Jpush->iosNotification($string, [
            'badge'  => '+1',
            'extras' => [
                'object_id' => $user->id,
                'type'      => 'upGoods'
            ]
        ]);
        //安卓推送
        $this->Jpush->androidNotification($string, [
            'title'  => '上新通知',
            'extras' => [
                'object_id' => $user->id,
                'type'      => 'upGoods'
            ]
        ]);
        //配置通知
        $this->Jpush->options(['apns_production' => $apnsProduction]);
        return $this->send();
    }

    /**
     *  黑名单上新通知
     */
    public function blackListNotice()
    {
        //配置安卓的测试环境的调试
        $string         = "奢多多同行黑名单已更新，注意查收！";
        $apnsProduction = true;
        if (count($this->DebugConfig) > 0) {
            //发送给的用户
            $this->DebugJpush->addAllAudience();
            //设置推送内容

            $this->DebugJpush->setNotificationAlert($string);
            //ios推送内容
            $this->DebugJpush->iosNotification($string, [
                'badge'  => '+1',
                'extras' => [
                    'object_id' => '',
                    'type'      => 'blackList'
                ]
            ]);
            //安卓推送
            $this->DebugJpush->androidNotification($string, [
                'title'  => '上新通知',
                'extras' => [
                    'object_id' => '',
                    'type'      => 'blackList'
                ]
            ]);
            //配置通知
            $apnsProduction = false;
            $this->DebugJpush->options(['apns_production' => $apnsProduction]);
        }
        //发送给的用户
        $this->Jpush = $this->addAllAudience();
        //设置推送内容
        $this->Jpush->setNotificationAlert($string);
        //ios推送内容
        $this->Jpush->iosNotification($string, [
            'badge'  => '+1',
            'extras' => [
                'object_id' => '',
                'type'      => 'blackList'
            ]
        ]);
        //安卓推送
        $this->Jpush->androidNotification($string, [
            'title'  => '上新通知',
            'extras' => [
                'object_id' => '',
                'type'      => 'blackList'
            ]
        ]);
        //配置通知
        $this->Jpush->options(['apns_production' => $apnsProduction]);
        return $this->send();
    }

    /**
     * @param $users 邀请过的人
     * @param $user 被邀请的人
     */
    public function invitationNotice(Collection $users, User $user)
    {
        //配置安卓的测试环境的调试
        $string         = "您邀请的通讯录好友“{$user->account}”已加入奢多多！";
        $apnsProduction = true;
        $sendUserid     = array_map(function ($v) {
            return $v->id;
        }, $users);
        if (count($this->DebugConfig) > 0) {
            //发送给的用户
            $this->DebugJpush->addTag($sendUserid);
            //设置推送内容

            $this->DebugJpush->setNotificationAlert($string);
            //ios推送内容
            $this->DebugJpush->iosNotification($string, [
                'badge'  => '+1',
                'extras' => [
                    'object_id' => $user->id,
                    'type'      => 'invitation'
                ]
            ]);
            //安卓推送
            $this->DebugJpush->androidNotification($string, [
                'title'  => '上新通知',
                'extras' => [
                    'object_id' => $user->id,
                    'type'      => 'invitation'
                ]
            ]);
            //配置通知
            $apnsProduction = false;
            $this->DebugJpush->options(['apns_production' => $apnsProduction]);
        }
        //发送给的用户
        $this->Jpush = $this->addTag($sendUserid->id);
        //设置推送内容
        $this->Jpush->setNotificationAlert($string);
        //ios推送内容
        $this->Jpush->iosNotification($string, [
            'badge'  => '+1',
            'extras' => [
                'object_id' => $user->id,
                'type'      => 'invitation'
            ]
        ]);
        //安卓推送
        $this->Jpush->androidNotification($string, [
            'title'  => '上新通知',
            'extras' => [
                'object_id' => $user->id,
                'type'      => 'invitation'
            ]
        ]);
        //配置通知
        $this->Jpush->options(['apns_production' => $apnsProduction]);
        return $this->send();
    }


    private function send()
    {
        if(count($this->DebugConfig) > 0)
        {
            $debugData=$this->DebugJpush->build();
        }
        $data=$this->Jpush->build();













    }
}