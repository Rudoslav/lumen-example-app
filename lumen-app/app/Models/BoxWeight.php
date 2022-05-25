<?php

namespace App\Models;

use App\Api\BoxWeightInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin Model
 */
class BoxWeight extends Model implements BoxWeightInterface
{
    use HasFactory;

    protected $table = BoxWeightInterface::TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'measured_at', 'message_number', 'weight_expected', 'weight_real', 'weight_expected_unit', 'weight_real_unit',
        'order_id', 'box_uid', 'picker_id', 'weight_real_sync_at'
    ];

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getCreatedAt(): string
    {
        return (string)$this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return (string)$this->updated_at;
    }

    public function getMeasuredAt(): int
    {
        return (int)$this->measured_at;
    }

    public function getMessageNumber(): int
    {
        return (int)$this->message_number;
    }

    public function getWeightExpected(): int
    {
        return (int)$this->weight_expected;
    }

    public function getWeightExpectedUnit(): string
    {
        return (string)$this->weight_expected_unit;
    }

    public function getWeightReal(): ?int
    {
        return $this->weight_real === null ? null : (int)$this->weight_real;
    }

    public function getWeightRealUnit(): string
    {
        return (string)$this->weight_real_unit;
    }

    public function getOrderId(): int
    {
        return (int)$this->order_id;
    }

    public function getBoxUID(): int
    {
        return (int)$this->box_uid;
    }

    public function getPickerId(): int
    {
        return (int)$this->picker_id;
    }

    public function getWeightRealSyncAt(): string
    {
        return (string)$this->weight_real_sync_at;
    }

    public function setMeasuredAt(int $measuredAt)
    {
        $this->measured_at = $measuredAt;
    }

    public function setMessageNumber(int $messageNumber)
    {
        $this->message_number = $messageNumber;
    }

    public function setWeightExpected(int $weightExpected)
    {
        $this->weight_expected = $weightExpected;
    }

    public function setWeightExpectedUnit(string $weightExpectedUnit)
    {
        $this->weight_expected_unit = $weightExpectedUnit;
    }

    public function setWeightReal(?int $weightReal)
    {
        $this->weight_real = $weightReal;
    }

    public function setWeightRealUnit(string $weightRealUnit)
    {
        $this->weight_real_unit = $weightRealUnit;
    }

    public function setOrderId(int $orderId)
    {
        $this->order_id = $orderId;
    }

    public function setBoxUID(int $boxUID)
    {
        $this->box_uid = $boxUID;
    }

    public function setPickerId(int $pickerId)
    {
        $this->picker_id = $pickerId;
    }

    public function setWeightRealSyncAt(?string $syncAt)
    {
        $this->weight_real_sync_at = $syncAt;
    }

    public function getPickedAt(): ?string
    {
        return $this->picked_at === null ? null : (string)$this->picked_at;
    }

    public function setPickedAt(?string $pickedAt)
    {
        $this->picked_at = $pickedAt;
    }
}
