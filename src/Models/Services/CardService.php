<?php

namespace WalkerChiu\DeviceRFID\Models\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Exceptions\NotExpectedEntityException;
use WalkerChiu\Core\Models\Exceptions\NotFoundEntityException;
use WalkerChiu\Core\Models\Services\CheckExistTrait;

class CardService
{
    use CheckExistTrait;

    protected $repository;



    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = App::make(config('wk-core.class.device-rfid.cardRepository'));
    }

    /*
    |--------------------------------------------------------------------------
    | Get card
    |--------------------------------------------------------------------------
    */

    /**
     * @param Int  $card_id
     * @return Card
     *
     * @throws NotFoundEntityException
     */
    public function find(int $card_id)
    {
        $entity = $this->repository->find($card_id);

        if (empty($entity))
            throw new NotFoundEntityException($entity);

        return $entity;
    }

    /**
     * @param Card|Int  $source
     * @return Card
     *
     * @throws NotExpectedEntityException
     */
    public function findBySource($source)
    {
        if (is_integer($source))
            $entity = $this->find($source);
        elseif (is_a($source, config('wk-core.class.device-rfid.card')))
            $entity = $source;
        else
            throw new NotExpectedEntityException($source);

        return $entity;
    }



    /*
    |--------------------------------------------------------------------------
    | Operation
    |--------------------------------------------------------------------------
    */

    /**
     * Check if it is within the validity period.
     *
     * @param Card|Int  $source
     * @param Carbon    $now
     * @return Array
     */
    public function checkTimeliness($source, Carbon $now)
    {
        $entity = $this->findBySource($source);

        if (
            $entity->begin_at->greaterThan($now)
            || $entity->end_at->lessThan($now)
        ) {
            return false;
        }

        if (!empty($entity->only_dayType)) {
            $dayType_full = [0, 1, 2, 3, 4, 5, 6];

            $only_dayType = [];
            foreach ($entity->only_dayType as $record)
                array_push($only_dayType, $record);

            $diff = array_diff($dayType_full, $only_dayType);

            foreach ($diff as $item) {
                if ($item == 0 && $now->isSunday())
                    return false;
                elseif ($item == 1 && $now->isMonday())
                    return false;
                elseif ($item == 2 && $now->isTuesday())
                    return false;
                elseif ($item == 3 && $now->isWednesday())
                    return false;
                elseif ($item == 4 && $now->isThursday())
                    return false;
                elseif ($item == 5 && $now->isFriday())
                    return false;
                elseif ($item == 6 && $now->isSaturday())
                    return false;
            }
        }

        if (!empty($entity->exclude_date)) {
            foreach ($entity->exclude_date as $record) {
                $date = Carbon::parse($record);
                if ($now->isSameDay($date))
                    return false;
            }
        }

        if (!empty($entity->exclude_time)) {
            $nowDate = $now->format('Y-m-d');
            foreach ($entity->exclude_time as $record) {
                $pair = explode('-', $record);
                $date_begin = Carbon::parse($nowDate .' '. $pair[0]);
                $date_end   = Carbon::parse($nowDate .' '. $pair[1]);

                if (
                    $now->greaterThanOrEqualTo($date_begin)
                    && $now->lessThanOrEqualTo($date_end)
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if it can be used.
     *
     * @param Card|Int  $source
     * @param Carbon    $now
     * @return Array
     */
    public function checkAvailability($source, Carbon $now)
    {
        $entity = $this->findBySource($source);

        if (
            $entity->is_enabled
            && $this->checkTimeliness($source, $now)
        ) {
            return true;
        } else {
            return false;
        }
    }
}
