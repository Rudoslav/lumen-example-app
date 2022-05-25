<?php

namespace App\Repositories;

use App\Api\BoxWeightInterface;
use App\Api\BoxWeightRepositoryInterface;
use App\Exceptions\Model\CouldNotSaveException;
use App\Models\BoxWeight;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BoxWeightRepository implements BoxWeightRepositoryInterface
{
    public function get(int $id): ?BoxWeightInterface
    {
        return BoxWeight::where(BoxWeightInterface::ID, $id)->first();
    }

    public function getByOrderId(int $orderId): ?BoxWeightInterface
    {
        return BoxWeight::where(BoxWeightInterface::ORDER_ID, $orderId)->first();
    }

    public function save(BoxWeightInterface $boxWeight)
    {
        DB::transaction(function () use ($boxWeight) {
            if (!$boxWeight->save()) {
                throw new CouldNotSaveException();
            }
        }, 3);
    }

    public function getByBoxUID(int $boxUID): ?BoxWeightInterface
    {
        return BoxWeight::where(BoxWeightInterface::BOX_UID, $boxUID)
		    ->orderBy(BoxWeightInterface::PICKED_AT, 'desc')
            ->first();
    }
}