<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Jorenvh\Share\Share;

class ShareController extends Controller
{
    public function shareWidget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|url',
        ]);

        if ($validator->fails())
            return response()->json([
                'status' => 'Bad Request',
                'msg' => 'Failed to fetch socials widget. Bad request',
                'errors' => $validator->errors(),
            ], 400);


        $shareLinks = \Share::page(
            $request->url,
            'Check out this amazing article!',
        )
            ->facebook()
            ->twitter()
            ->linkedin()
            ->telegram()
            ->whatsapp()
            ->reddit()
            ->getRawLinks();

        return response()->json([
            'status' => 'OK',
            'msg' => 'Sucessfully fetched social links',
            'links' => $shareLinks,
        ], 200);
    }
}
