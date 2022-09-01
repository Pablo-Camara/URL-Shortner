<?php

namespace App\Http\Controllers;

use App\Models\Shortlink;
use App\Models\Shortstring;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class ShortlinkController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function visit($shortstring, Request $request) {

        $shortstring = Shortstring::where('shortstring', '=', $shortstring)->with('shortlink')->first();

        if ($shortstring) {
            $shortlink = $shortstring['shortlink'];

            if (!is_null($shortlink)) {
                return Redirect::to(
                    $shortlink->long_url, 301
                );
            }

            if ($shortstring->is_available) {
                return view('home', [
                    'shortlink' => url('/' . $shortstring->shortstring),
                    'shortlink_available' => true,
                    'shortlink_shortstring' => $shortstring->shortstring
                ]);
            }

        }
        // shortstring not available
        return redirect('/');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //TODO: pagination
        $userShortlinks = $request->user()->shortlinks()
        ->select(['id', 'long_url', 'destination_email', 'shortstring_id'])
        ->with(
            [
                'shortstring' => function ($query) {
                    $query->select(['id','shortstring']);
                }
            ]
         )
         ->orderBy('id', 'desc')
         ->get()->toArray();

        $userShortlinks = array_map(
            function ($row) {
                return [
                    'id' => $row['id'],
                    'long_url' => $row['long_url'],
                    'shortlink' => URL::to('/' . $row['shortstring']['shortstring']),
                    'destination_email' => $row['destination_email']
                ];
            }, $userShortlinks
        );
        return new Response($userShortlinks);
    }

    /**
     * Register/point available shortstring to a long url
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerAvailable(Request $request)
    {
        // url validation, read below ( about max length )
        // https://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers
        $request->validate([
            'long_url' => 'required|url|max:2048',
        ]);

        $shortstring = $request->input('shortstring');

        $shortstring = Shortstring::where('shortstring', '=', $shortstring)->first();

        if (!$shortstring) {
            return new Response([
                //TODO: translate
                'message' => 'Este link não está disponível!'
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if (!$shortstring->is_available) {
            return new Response([
                //TODO: translate
                'message' => 'Este link já não está disponível!'
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }


        $newShortlink = new Shortlink();
        $newShortlink->user_id = $request->user()->id;
        $newShortlink->shortstring_id = $shortstring->id;
        $newShortlink->long_url = $request->input('long_url');
        $newShortlink->status_id = Shortlink::STATUS_ACTIVE;

        DB::beginTransaction();
        try {
            $newShortlink->save();
            $shortstring->is_available = 0;
            $shortstring->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        DB::commit();

        return new Response(
            [
                'shortlink' => URL::to('/' . $shortstring->shortstring)
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // url validation, read below ( about max length )
        // https://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers
        $request->validate([
            'long_url' => 'required|url|max:2048',
            'destination_email' => 'required|email:rfc,dns',
        ]);

        $nextAvailableShortstring = Shortstring::where('is_available', 1)->first();

        if (!$nextAvailableShortstring) {
            // this should never happen
            // but in case we ever run out of shortstrings/available links
            // we should be notified.

            //TODO: Send email notification.

            return new Response(
                [
                    'message' => 'Estamos sem links disponíveis! Volte a tentar dentro de alguns minutos!'
                ],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        $newShortlink = new Shortlink();
        $newShortlink->user_id = $request->user()->id;
        $newShortlink->shortstring_id = $nextAvailableShortstring->id;
        $newShortlink->long_url = $request->input('long_url');
        $newShortlink->destination_email = $request->input('destination_email');
        $newShortlink->status_id = Shortlink::STATUS_ACTIVE;

        DB::beginTransaction();
        try {
            $newShortlink->save();
            $nextAvailableShortstring->is_available = 0;
            $nextAvailableShortstring->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        DB::commit();

        return new Response(
            [
                'shortlink' => URL::to('/' . $nextAvailableShortstring->shortstring)
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
