<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Laracasts\Validation\FormValidator;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;


class YoutubeController extends Controller
{
    public function getModelName()
    {
        return 'Youtube';
    }

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->req = $request;
    }

    /**
     * @SWG\Get(
     *     tags={"Youtube"},
     *     path="/youtube",
     *     description="Return Youtube video ID",
     *     summary="Get video ID",
     *     operationId="GetVideo",
     *     produces={
     *         "application/json"
     *     },
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal Server Error",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */
    public function list()
    {

        $API_key    = 'AIzaSyDh228O5PTd3FzOL0bYrRZd4Jw4VPT66qQ';
        $playlistID1 = 'PL--opZe_r0UaLH2_wvvfo0Ecxs8kmeYu9';
        $playlistID2 = 'PL--opZe_r0UZLrV06QmlOwZUWQiApW79n';
        $maxResults = 1;

        try
        {

            $videoID1 = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?order=date&part=snippet&maxResults=1&playlistId='. $playlistID1 . '&key=' . $API_key));

            $videoID2 = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?order=date&part=snippet&maxResults=1&playlistId='. $playlistID2 . '&key=' . $API_key));


            $response = [
                'recent_video_id' => $videoID1->items[0]->snippet->resourceId->videoId,
                'recent_video_title' => $videoID1->items[0]->snippet->title,
                'live_video_id' => $videoID2->items[0]->snippet->resourceId->videoId,
                'live_video_title' => $videoID2->items[0]->snippet->title
            ];

            return $this->sendResponse(200,$response);
        }
        catch (Exception $e) 
        {
            $response = [
                'recent_video_id' => 'recent_video_id',
                'recent_video_title' => 'recent_video_title',
                'live_video_id' => 'live_video_id',
                'live_video_title' => 'live_video_title'
            ];

            return $this->sendResponse(200,$response);
        }
    }

    public function option()
    {    
        return $this->sendResponse();   
    }
}
