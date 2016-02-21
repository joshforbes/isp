<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'duration',
        'start_at',
        'end_at',
    ];

    /**
     * The attributes that should be cast to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_at',
        'end_at',
        'deleted_at'
    ];

    /**
     * Get poop time in a readable format.
     *
     * @return string
     */
    public function readableDuration()
    {
        $seconds = $this->duration % 60;
        $minutes = floor(($this->duration % 3600)/60);

        return sprintf('%s minutes %s seconds', $minutes, $seconds);
    }

    /**
     * @param $type
     *
     * @return bool
     */
    public function isActive($type)
    {
        $active = $this->where('type', $type)->whereNull('end_at')->first();

        if ($active) {
            return true;
        }

        return false;
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public function active($type)
    {
        return $this->where('type', $type)->whereNull('end_at')->first();
    }

    /**
     * @param $type
     *
     * @return int
     */
    public function allTime($type)
    {
        return count($this->where('type', $type)->get());
    }

    /**
     * @param $type
     *
     * @return string
     */
    public function averageTime($type)
    {
        $entries = $this->where('type', $type)->get();
        $count = count($entries);

        $totalDuration = $entries->reduce(function($carry, $value) {
            return $carry + $value->duration;
        });

        $average = $totalDuration / $count;

        $seconds = $average % 60;
        $minutes = floor(($average % 3600)/60);

        return sprintf('%s minutes %s seconds', $minutes, $seconds);
    }

    /**
     * @return mixed
     */
    public function record($type)
    {
        return $this->where('type', $type)->orderBy('duration', 'desc')->first();
    }

}
