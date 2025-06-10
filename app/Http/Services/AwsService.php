<?php

namespace App\Http\Services;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\Sns\SnsClient;

class AwsService
{
    /**
     * @param $message
     * @param $topicArn
     * @return Result
     */
    function publishMessageToSns($message, $topicArn): Result
    {
        $snsClient = new SnsClient([
            'region'  => 'us-west-1',
            'version' => 'latest',
        ]);

        return $snsClient->publish([
            'Message' => json_encode($message),
            'TopicArn' => $topicArn,
        ]);
    }

}
