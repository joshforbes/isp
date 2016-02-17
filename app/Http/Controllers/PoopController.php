<?php

namespace App\Http\Controllers;

use App\Exceptions\IsNotCurrentlyPooping;
use App\Exceptions\IsStillPooping;
use App\Poop;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PoopController extends BaseController
{
    private $poop;

    /**
     * PoopController constructor.
     *
     * @param Poop $poop
     */
    public function __construct(Poop $poop)
    {
        $this->poop = $poop;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $isPooping = $this->poop->isPooping();
        $mostRecentPoop = $this->poop->orderBy('end_at', 'desc')->first();
        $recordPoop = $this->poop->orderBy('duration', 'desc')->first();

        return view('welcome', [
            'isPooping' => $isPooping,
            'mostRecentPoop' => $mostRecentPoop,
            'recordPoop' => $recordPoop,
            'lifetimePoops' => $this->poop->allTimePoops(),
            'averagePoopTime' => $this->poop->averagePoopTime()
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function start()
    {
        if (request('token') != env('SLACK_TOKEN')) {
            return response()->json('This only works from the appropriate Slack channel');
        }

        if ($this->poop->isPooping()) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'Can\'t start a new Poop. Stuart is still Pooping.'
            ]);
        }

        $this->poop->create([
            'start_at' => time()
        ]);

        return response()->json([
            'response_type' => 'in_channel',
            'text' => 'Here we go, Stuart is going for the record!'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function stop()
    {
        if (request('token') != env('SLACK_TOKEN')) {
            return response()->json('This only works from the appropriate Slack channel');
        }

        if (!$this->poop->isPooping()) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'Can\'t stop a poop. Stuart is not currently Pooping.'
            ]);
        }

        $poop = $this->poop->currentPoop();
        $recordPoop = $this->poop->orderBy('duration', 'desc')->first();

        $poop->end_at = Carbon::now();
        $poop->duration = $poop->end_at->timestamp - $poop->start_at->timestamp;
        $poop->save();

        $message = $poop->duration > $recordPoop->duration ? 'A new record!' : '';

        return response()->json([
            'response_type' => 'in_channel',
            'text' => sprintf('He is all done, that took %s. %s', $poop->readableDuration(), $message)
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $recordPoop = $this->poop->recordPoop();
        $lifetimePoops = $this->poop->allTimePoops();
        $mostRecentPoop = $this->poop->orderBy('end_at', 'desc')->first();

        return response()->json([
            'response_type' => 'in_channel',
            'text' => sprintf(
                'Most recent poop: %s.
                It took: %s.\n
                All-time record Poop: %s.
                Lifetime Poops: %s.
                Average Poop time: %s',
                $mostRecentPoop->end_at->diffForHumans(),
                $mostRecentPoop->readableDuration(),
                $recordPoop->readableDuration(),
                $lifetimePoops,
                $this->poop->averagePoopTime()
            )
        ]);
    }
}
