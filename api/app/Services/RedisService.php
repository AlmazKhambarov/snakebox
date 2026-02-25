<?php

namespace App\Services;

use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

class RedisService
{
    protected Connection $redis;
    protected string $prefix = 'case_battle';
    public function __construct()
    {
        $this->redis = Redis::connection();
    }

    public function get(string $key)
    {
        $value = $this->redis->get($this->prefix . ':' . $key);
        return $value ? json_decode($value, true) : null;
    }

    public function set(string $key, $value, ?int $ttl = null): void
    {
        $value = json_encode($value);
        if ($ttl) {
            $this->redis->setex($this->prefix . ':' . $key, $ttl, $value);
        } else {
            $this->redis->set($this->prefix . ':' . $key, $value);
        }
    }

    public function delete(string $key): void
    {
        $this->redis->del($key);
    }

    public function lrange(string $key, int $start, int $end)
    {
        return $this->redis->lrange($this->prefix . ':' . $key, $start, $end);
    }

    public function rpush(string $key, $value): void
    {
        $this->redis->rpush($this->prefix . ':' . $key, json_encode($value));
    }

    public function publish(string $channel, array $message)
    {
        $this->redis->publish($channel, json_encode($message));
    }

    public function lrem(string $key, int $count, $value)
    {
        $this->redis->lrem($this->prefix . ':' . $key, $count, json_encode($value));
    }

    public function updateUserBalance(int $user_id, float $balance, int $event_points): void
    {
        $this->publish('userBalance', [
            'user_id' => $user_id,
            'balance' => $balance,
            'event_points' => $event_points
        ]);
    }
}
