<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Contacts as ContactsResource;
use App\Http\Resources\ContactsCollection;
use App\Contacts;

class ContactsController extends Controller
{
    public function index()
    {
        try {
            $contacts = Contacts::orderBy('created_at', 'DESC')->paginate(30);

            return response()->json($contacts, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // - I am not doing validation here
        $post = new Contacts();
        // $post->name = $request->name;
        // $post->email = $request->email;
        // $post->phone = $request->phone;
        // $post->industry = $request->industry;
        // $post->organisation_size = $request->organisation_size;
        // $post->message = $request->message;
        $post->save($request->all());
        
        return new ContactsResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new ContactsResource(Contacts::where('id', $id)->first());
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
        //I am doing validation here
        $post = Contacts::findOrFail($id);
        // $post->name = $request->name;
        // $post->email = $request->email;
        // $post->phone = $request->phone;
        // $post->industry = $request->industry;
        // $post->organisation_size = $request->organisation_size;
        // $post->message = $request->message;
        $post->update($request->all());
        
        return new ContactsResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Contacts::findOrFail($id);
        $post->delete();
        
        return new ContactsResource($post);
    }

        
    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Contacts::find($request->ids)->each(function ($location, $key) {
                    $location->delete();
                });

                return response()->json('Successfully deleted pages.', 200);
            } else response()->json('Operation Not permitted', 400); 
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    // - Done
    public function searchData(Request $request) {
        try { 
            if (!empty($request->q)) {
                $data = Contacts::where(
                    'name', 'LIKE', '%'.$request->q.'%'
                )->paginate(30);

                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
    public function subscribeNewsletter(Request $request){
       $key = env('MAILERLITE');
       $name = $request->name;
       $group = $request->group;
       $email = $request->email;
       $subscriber = array('email' => $email,'name' => $name);

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,"https://api.mailerlite.com/api/v2/groups/".$group."/subscribers");
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($subscriber)); 
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       $headers = [];

       curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-MailerLite-ApiKey:2d5cbff4afddc1e809204a6a155985f5','Content-Type:application/json'));

       $server_output = curl_exec($ch);    
       
       curl_close ($ch);
       
       $server_output = json_decode($server_output,true);
       $response = [];
       if(isset($server_output['error']))
       {
           $response = ['status'=>0,'error'=>$server_output['error']['message'],'data'=>[]];
       }
       else
       {
           $response = ['status'=>1,'error'=>[],'data'=>[]];
       }
        return response()->json($response, 200);
    }
}
