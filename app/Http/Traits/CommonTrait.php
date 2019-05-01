<?php
namespace App\Http\Traits;

use Image;
use App\Models\Notification;
use App\Models\UserSkill;
use App\Models\UserGenre;


trait CommonTrait
{
    public function imageDynamicName()
    {
        #Available alpha caracters
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pin = mt_rand(1000000, 9999999)
            . $characters[rand(0, 5)];
        $string = str_shuffle($pin);
        return $string;
    }
    public function categoryImageVersions($name)
    {
        $main_dir = storage_path() . '/app/public/category';
        $thumb_dir = storage_path() . '/app/public/category/thumb';
        if (!file_exists($thumb_dir)) {
            mkdir($thumb_dir, 0777);
            chmod($thumb_dir, 0777);
        }
        if (file_exists($main_dir . '/' . $name)) {
            chmod($main_dir . '/' . $name, 0777);
            Image::make($main_dir . '/' . $name)->resize(110, 110)->save($thumb_dir . '/' . $name);
            chmod($thumb_dir . '/' . $name, 0777);
        }
        return $name;
    }
    public function imageVersions($name)
    {
        $main_dir = storage_path() . '/app/public/category/categorycomplete';
        $thumb_dir = storage_path() . '/app/public/category/categorycomplete/thumb';

        if (!file_exists($thumb_dir)) {
            mkdir($thumb_dir, 0777);
            chmod($thumb_dir, 0777);
        }

        if (file_exists($main_dir . '/' . $name)) {
            chmod($main_dir . '/' . $name, 0777);
            Image::make($main_dir . '/' . $name)->resize(110, 110)->save($thumb_dir . '/' . $name);
            chmod($thumb_dir . '/' . $name, 0777);
        }
        return $name;
    }
 

    public function notifications($requested_data,$user_id,$challenge_id,$type,$recv_id)
    {
       
        $notification = Notification::Create([
            'sender_id' => $user_id,
            'receiver_id' =>$recv_id,
            'challenge_id' => $challenge_id,
            'type' =>$type,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        if ($notification) {
            return true;
        } else {
            return false;
        }
    }

    # get friends basis on user id 
    public function userFriends($user_id)
    {
     $getFriends = Friend::where('owner_user_id',$user_id)
                   ->whereHas('friend', function($query) {
                    $query->where('status',1);
                  })
                  ->get()->toArray();

        $collection = collect($getFriends)->map(function ($name) {
            return $name["friend_id"];
        });
        $friends_id = $collection->toArray();  

        return   $friends_id ;    
    }

     // save user genres
    private function saveUserGenres($id,$data){
        foreach($data as $key=>$val){
           $array[$key]['genre_id']=$val; 
           $array[$key]['user_id']=$id; 
           $array[$key]['created_at']=time(); 
        }
        UserGenre::Insert($array);
    }

     
        // send notification
        public function sendNotification($requested_data,$user_ids=array(),$user_id=0,$episode_id=0)
        {
            
            if(count($user_ids)){
                foreach ($user_ids as $key => $value) {
                    $insert[$key] = ['sender_id'=>$requested_data['data']['id'],'receiver_id'=>$value,'created_at'=>time(),'type'=>$requested_data['type'],'story_id'=>$requested_data['story_id'],'episode_id'=>$episode_id];
                }
                Notification::insert($insert);
                return 1;
            }
    
            if($user_id){
                    Notification::Create(['sender_id'=>$requested_data['data']['id'],'receiver_id'=>$user_id,'created_at'=>time(),'type'=>$requested_data['type'],'story_id'=>$requested_data['story_id'],'episode_id'=>$episode_id]);
                    return 1;
            }
            return 0;
            //return Response::json($data);
        }

    // delete job videos
    private function deleteJobVideos($job_ids){
        if(is_array($job_ids) && count($job_ids)){
            foreach ($job_ids as $key => $value) {    
                $job_data = UserJob::where('id',$value)->first();
                $job_path = storage_path('app/public/job_videos/') . $job_data->video_url;
                if ($job_data->video_url != '' && $job_data->video_url != null) {
                        if (file_exists($job_path)) {
                            unlink($$job_path);
                        }
                }
            }
        }
    }

    // delete cv
    private function deleteCvVideos($id){
        $data = UserCv::where('id',$id)->first();
        $path = storage_path('app/public/cv/') . $data->url;
        if ($data->url != '' && $data->url != null) {
                if (file_exists($path)) {
                    unlink($path);
                }
        }
    }


    // delete logo
    private function deleteLogo($id){
        $data = User::where('id',$id)->first();
        $path = storage_path('app/public/images/logo') . $data->profile_image;
        if ($data->profile_image != '' && $data->profile_image != null) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

       // delete user image
    private function deleteUserImage($id){
        $data = User::where('id',$id)->first();
        $path = storage_path('app/public/images/user') . $data->profile_image;
        if ($data->profile_image != '' && $data->profile_image != null) {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

                
}
