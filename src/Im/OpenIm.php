<?php

namespace cccdl\tencent_im_sdk\Im;

use cccdl\tencent_im_sdk\Exception\cccdlException;

/**
 * 单聊信息
 * Class OpenIm
 * @package cccdl\tencent_im_sdk\Im
 */
class OpenIm extends Im
{
    //文本消息
    const TXT = 'TIMTextElem';
    //表情消息
    const FACE = 'TIMFaceElem';
    //位置消息
    const LOCATION = 'TIMLocationElem';
    //自定义消息
    const CUSTOM = 'TIMCustomElem';

    /**
     * 内部服务名，不同的 serviceName 对应不同的服务类型
     * @var string
     */
    protected $serviceName = 'openim';

    /**
     * 管理员向帐号发消息，接收方看到消息发送者是管理员。
     * 管理员指定某一帐号向其他帐号发消息，接收方看到发送者不是管理员，而是管理员指定的帐号。
     * 该接口不会检查发送者和接收者的好友关系（包括黑名单），同时不会检查接收者是否被禁言。
     *
     * - 管理员向其它帐号发消息
     *   注意：若不希望将消息同步至 From_Account，则 SyncOtherMachine 填写2；若希望将消息同步至 From_Account，则 SyncOtherMachine 填写1。
     *
     * - 管理员向其它帐号发消息，同时指定消息不回调
     *   注意：若不希望将消息同步至 From_Account，则 SyncOtherMachine 填写2；若希望将消息同步至 From_Account，则 SyncOtherMachine 填写1。
     *
     * - 管理员指定某一帐号向其它帐号发送消息，同时设置离线推送信息，并且不将消息同步至 From_Account
     *   注意：若不希望将消息同步至 From_Account，则 SyncOtherMachine 填写2。
     *
     * - SyncOtherMachine Integer 选填
     *   1：把消息同步到 From_Account 在线终端和漫游上；
     *   2：消息不同步至 From_Account；
     *   若不填写默认情况下会将消息存 From_Account 漫游
     *
     * - From_Account String 选填
     *   消息发送方 UserID（用于指定发送消息方帐号）
     *
     * - To_Account String 必填
     *   消息接收方 UserID
     *
     * - To_Account String 必填
     *   消息接收方 UserID
     *
     * - MsgLifeTime Integer 选填
     *   消息离线保存时长（单位：秒），最长为7天（604800秒）
     *   若设置该字段为0，则消息只发在线用户，不保存离线
     *   若设置该字段超过7天（604800秒），仍只保存7天
     *   若不设置该字段，则默认保存7天
     *
     * - MsgRandom Integer 必填
     *   消息随机数，由随机函数产生，用于后台定位问题
     *
     * - MsgTimeStamp Integer 选填
     *   消息时间戳，UNIX 时间戳（单位：秒）
     *
     * - ForbidCallbackControl Array 选填
     *   消息回调禁止开关，只对本条消息有效，
     *   ForbidBeforeSendMsgCallback 表示禁止发消息前回调，
     *   ForbidAfterSendMsgCallback 表示禁止发消息后回调
     *
     * - MsgBody Object 必填
     *   消息内容，具体格式请参考 消息格式描述（注意，一条消息可包括多种消息元素，MsgBody 为 Array 类型）
     *   https://cloud.tencent.com/document/product/269/2720
     *
     * - MsgType String 必填
     *   TIM 消息对象类型，目前支持的消息对象包括：TIMTextElem(文本消息)，TIMFaceElem(表情消息)，TIMLocationElem(位置消息)，TIMCustomElem(自定义消息)
     *
     * - MsgContent Object 必填
     *   对于每种 MsgType 用不同的 MsgContent 格式，具体可参考 消息格式描述
     *   https://cloud.tencent.com/document/product/269/2720
     *
     * - OfflinePushInfo Object 选填
     *   离线推送信息配置，具体可参考 消息格式描述
     *   https://cloud.tencent.com/document/product/269/2720
     *
     *
     * @param string $fromAccount 发送方
     * @param string $toAccount 接收方
     * @param string $type 消息类型
     * @param array $msgBody 消息体
     * @param array $options 额外配置参数
     * @return array
     * @throws cccdlException
     */
    public function sendMsg(string $fromAccount, string $toAccount, string $type, array $msgBody, array $options = [])
    {
        $this->command = 'sendmsg';

        $url = $this->getUrl();

        $time = time();

        $param = [
            'From_Account' => $fromAccount,
            'To_Account' => $toAccount,
            'MsgRandom' => $time,
            'MsgTimeStamp' => $time,
            'MsgBody' => [
                [
                    'MsgType' => $type,
                    'MsgContent' => $msgBody
                ]
            ]
        ];

        //合并额外参数
        $param = array_merge($param, $options);

        return $this->post($url, $param);

    }

    /**
     * 支持一次对最多500个用户进行单发消息。
     * 与单发消息相比，该接口更适用于营销类消息、系统通知 tips 等时效性较强的消息。
     * 管理员指定某一帐号向目标帐号批量发消息，接收方看到发送者不是管理员，而是管理员指定的帐号。
     * 该接口不触发回调请求。
     * 该接口不会检查发送者和接收者的好友关系（包括黑名单），同时不会检查接收者是否被禁言。
     * @param string $fromAccount
     * @param array $toAccount
     * @param string $type
     * @param array $msgBody
     * @param array $options
     * @return array
     * @throws cccdlException
     */
    public function batchSendMsg(string $fromAccount, array $toAccount, string $type, array $msgBody, array $options = [])
    {
        $this->command = 'batchsendmsg';

        $url = $this->getUrl();

        $time = time();

        $param = [
            'From_Account' => $fromAccount,
            'To_Account' => $toAccount,
            'MsgRandom' => $time,
            'MsgBody' => [
                [
                    'MsgType' => $type,
                    'MsgContent' => $msgBody
                ]
            ]
        ];

        //合并额外参数
        $param = array_merge($param, $options);

        return $this->post($url, $param);
    }
}