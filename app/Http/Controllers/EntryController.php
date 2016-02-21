<?php

namespace App\Http\Controllers;

use App\Entry;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EntryController extends BaseController
{
    private $entry;

    /**
     * PoopController constructor.
     *
     * @param Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function start()
    {
        if (request('token') != env('SLACK_START_TOKEN')) {
            return response()->json('This only works from the appropriate Slack channel');
        }

        $type = request()->text;

        if (!$type) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'You must specify a type.'
            ]);
        }

        if ($this->entry->isActive($type)) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'Can\'t start a new Recording for ' . $type . '. A recording is currently active.'
            ]);
        }

        $this->entry->create([
            'start_at' => time(),
            'type' => $type
        ]);

        return response()->json([
            'response_type' => 'in_channel',
            'text' => 'Timer has been started for ' . $type . '.'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function stop()
    {
        if (request('token') != env('SLACK_STOP_TOKEN')) {
            return response()->json('This only works from the appropriate Slack channel');
        }

        $type = request()->text;

        if (!$type) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'You must specify a type.'
            ]);
        }

        if (!$this->entry->isActive($type)) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'Can\'t stop a recording for ' . $type . '. Not currently recording.'
            ]);
        }

        $entry = $this->entry->active($type);
        $record = $this->entry->record($type);

        $entry->end_at = Carbon::now();
        $entry->duration = $entry->end_at->timestamp - $entry->start_at->timestamp;
        $entry->save();

        $message = $entry->duration > $record->duration ? 'A new record!' : '';

        return response()->json([
            'response_type' => 'in_channel',
            'text' => sprintf(
                'Finished recording for ' . $type . ', that took %s. %s',
                $entry->readableDuration(),
                $message)
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel()
    {
        if (request('token') != env('SLACK_CANCEL_TOKEN')) {
            return response()->json('This only works from the appropriate Slack channel');
        }

        $type = request()->text;

        if (!$type) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'You must specify a type.'
            ]);
        }

        if (!$this->entry->isActive($type)) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'Can\'t stop a recording for ' . $type . '. Not currently recording.'
            ]);
        }

        $entry = $this->entry->active($type);
        $entry->delete();


        return response()->json([
            'response_type' => 'in_channel',
            'text' => 'Canceled the recording for ' . $type . '.'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $type = request()->text;

        if (!$type) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'You must specify a type.'
            ]);
        }

        try {
            $record = $this->entry->record($type);
            $lifetime = $this->entry->allTime($type);
            $mostRecent = $this->entry->where('type', $type)->orderBy('end_at', 'desc')->firstOrFail();
        } catch (\Exception $e) {
            return response()->json([
                'response_type' => 'in_channel',
                'text' => 'There are no recordings for ' . $type . '.'
            ]);
        }

        return response()->json([
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'title' => 'Stats for ' . $type,
                    'fields' => [
                        [
                            'title' => 'Most Recent',
                            'value' => $mostRecent->end_at->diffForHumans(),
                            'short' => 'true'
                        ],
                        [
                            'title' => 'Most Recent Duration',
                            'value' => $mostRecent->readableDuration(),
                            'short' => 'true'
                        ],
                        [
                            'title' => 'Lifetime',
                            'value' => $lifetime,
                            'short' => 'true'
                        ],
                        [
                            'title' => 'Average Duration',
                            'value' => $this->entry->averageTime($type),
                            'short' => 'true'
                        ],
                        [
                            'title' => 'All-Time Record',
                            'value' => $record->readableDuration(),
                        ],
                    ],
                    "color" =>  "#FF0000"
                ]
            ]
        ]);
    }
}
