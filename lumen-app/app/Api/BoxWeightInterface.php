<?php

namespace App\Api;

interface BoxWeightInterface
{
    public const ID = 'id';
    public const ORDER_ID = 'order_id';
    public const BOX_UID = 'box_uid';
    public const PICKER_ID = 'picker_id';
    public const WEIGHT_REAL = 'weight_real';
    public const WEIGHT_REAL_SYNC_AT = 'weight_real_sync_at';
	public const PICKED_AT = 'picked_at';
    public const TABLE = 'box_weight';
    public const MAX_SYC_ITEMS = 400;

    public function getId(): int;
    public function getCreatedAt(): string;
    public function getUpdatedAt(): string;
    public function getMeasuredAt(): int;
    public function getMessageNumber(): int;
    public function getWeightExpected(): int;
    public function getWeightExpectedUnit(): string;
    public function getWeightReal(): ?int;
    public function getWeightRealUnit(): string;
    public function getOrderId(): int;
    public function getBoxUID(): int;
    public function getPickerId(): int;
    public function getWeightRealSyncAt(): string;
	public function getPickedAt(): ?string;

    public function setCreatedAt(string $createdAt);
    public function setUpdatedAt(string $updatedAt);
    public function setMeasuredAt(int $measuredAt);
    public function setMessageNumber(int $messageNumber);
    public function setWeightExpected(int $weightExpected);
    public function setWeightExpectedUnit(string $weightExpectedUnit);
    public function setWeightReal(?int $weightReal);
    public function setWeightRealUnit(string $weightRealUnit);
    public function setOrderId(int $orderId);
    public function setBoxUID(int $boxUID);
    public function setPickerId(int $pickerId);
    public function setWeightRealSyncAt(?string $syncAt);
    public function setPickedAt(?string $pickedAt);
}