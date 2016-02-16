<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poop extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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

    public function isPooping()
    {
        $activePoop = $this->whereNull('end_at')->first();

        if ($activePoop) {
            return true;
        }

        return false;
    }

    public function currentPoop()
    {
        return $this->whereNull('end_at')->first();
    }

    public function allTimePoops()
    {
        return count($this->all());
    }

    public function averagePoopTime()
    {
        $poops = $this->all();
        $count = count($poops);

        $totalDuration = $poops->reduce(function($carry, $value) {
            return $carry + $value->duration;
        });

        $average = $totalDuration / $count;

        $seconds = $average % 60;
        $minutes = floor(($average % 3600)/60);

        return sprintf('%s minutes %s seconds', $minutes, $seconds);
    }

}
