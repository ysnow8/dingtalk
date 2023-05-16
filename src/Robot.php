<?php

namespace Ysnow\Dingtalk;

class Robot
{

    protected $accessToken;

    protected $secret;

    private $dingdingWebHook = 'https://oapi.dingtalk.com/robot/send';

    public function __construct($accessToken, $secret = null)
    {
        $this->accessToken = $accessToken;
        $this->secret      = $secret;
    }

    /**
     * text类型
     *
     * @Date 2023/5/16
     *
     * @param $text
     * @param $isAtAll
     *
     * @return bool
     */
    public function sendMessage($text, $isAtAll = false)
    {
        $message = [
            'msgtype' => 'text',
            'text'    => [
                'content' => $text,
            ],
            'at'      => [
                'isAtAll' => $isAtAll,
            ],
        ];
        $resp    = $this->httpPost(json_encode($message));

        return $resp->errcode == 0 ? true : false;
    }

    /**
     * link类型
     *
     * @Date 2023/5/16
     *
     * @param $text
     * @param $title
     * @param $picUrl
     * @param $messageUrl
     * @param $isAtAll
     *
     * @return bool
     */
    public function sendLink($text, $title, $picUrl, $messageUrl, $isAtAll = false)
    {
        $message = [
            'msgtype' => 'link',
            'link'    => [
                'text'       => $text,
                'title'      => $title,
                'picUrl'     => $picUrl,
                'messageUrl' => $messageUrl
            ],
            'at'      => [
                'isAtAll' => $isAtAll,
            ],
        ];
        $resp    = $this->httpPost(json_encode($message));

        return $resp->errcode == 0 ? true : false;
    }

    /**
     * markdown类型
     *
     * @Date 2023/5/16
     *
     * @param $text
     * @param $title
     * @param $isAtAll
     *
     * @return bool
     */
    public function sendMarkdown($text, $title, $isAtAll = false)
    {
        $message = [
            'msgtype'  => 'markdown',
            'markdown' => [
                'text'  => $text,
                'title' => $title,
            ],
            'at'       => [
                'isAtAll' => $isAtAll,
            ],
        ];
        $resp    = $this->httpPost(json_encode($message));

        return $resp->errcode == 0 ? true : false;
    }


    /**
     * 整体跳转ActionCard类型
     *
     * @Date 2023/5/16
     *
     * @param $text
     * @param $title
     * @param $btnOrientation
     * @param $singleTitle
     * @param $singleURL
     * @param $isAtAll
     *
     * @return bool
     */
    public function sendActionCard($text, $title, $btnOrientation = 0, $singleTitle = '阅读原文', $singleURL = null, $isAtAll = false)
    {
        $message = [
            'msgtype'    => 'actionCard',
            'actionCard' => [
                'title'          => $title,
                'text'           => $text,
                'btnOrientation' => $btnOrientation,
                'singleTitle'    => $singleTitle,
                'singleURL'      => $singleURL

            ],
            'at'         => [
                'isAtAll' => $isAtAll,
            ],
        ];
        $resp    = $this->httpPost(json_encode($message));

        return $resp->errcode == 0 ? true : false;
    }


    /**
     * FeedCard类型
     *
     * @Date 2023/5/16
     *
     * @param $links
     * @param $isAtAll
     *
     * @return bool
     */
    public function sendFeedCard($links, $isAtAll = false)
    {
        $message = [
            'msgtype'  => 'feedCard',
            'feedCard' => [
                'links' => $links
            ],
            'at'       => [
                'isAtAll' => $isAtAll,
            ],
        ];
        $resp    = $this->httpPost(json_encode($message));

        return $resp->errcode == 0 ? true : false;
    }

    private function httpPost($postString)
    {
        $url = $this->dingdingWebHook.'?access_token='.$this->accessToken;
        if ($this->secret) {
            $timestamp = time().'000';
            $url       .= sprintf(
                '&sign=%s&timestamp=%s',
                urlencode(base64_encode(hash_hmac('sha256',
                    $timestamp."\n".$this->secret, $this->secret, true))),
                $timestamp
            );
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
                    ['Content-Type: application/json;charset=utf-8']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);

        return $data;
    }
}