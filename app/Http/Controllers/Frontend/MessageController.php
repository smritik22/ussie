<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

use App\Models\MainUsers as MainUser;
use App\Models\Property;
use App\Models\Setting;
use App\Models\UserConversation;

use Mail;
use File;
use Storage;
use Str;
use Helper;
use Auth;
use DB;
use Carbon\Carbon;

class MessageController extends Controller
{
    protected $chat_per_page;
	protected $messages_per_page;

	public function __construct()
	{
		$this->chat_per_page = 10;
		$this->messages_per_page = 20;
	}

    public function index() {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();

        $PageTitle = $labels['chat_message'];
        $PageDescription = "";
        $PageKeywords = "";
        $WebmasterSettings = "";
        return view('frontEnd.users.chat.chat_list', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings'));
    }

    public function fetchChatList(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();
        $page_no = @$request->input('chat_page_no') ?: 1;
        $chat_user_id = $request->input('chat_user_id');
        if($chat_user_id) {
            $chat_user_id = decrypt($chat_user_id);
        }

        $chat_list = UserConversation::from('user_conversation as m1')
			->select('m1.*')
			->join(DB::raw(
				'(
                SELECT
                    LEAST(from_id, to_id) AS from_id,
                    GREATEST(from_id, to_id) AS to_id,
                    MAX(id) AS max_id
                FROM user_conversation 
                GROUP BY
                    LEAST(from_id, to_id),
                    GREATEST(from_id, to_id)
            ) AS m2'
			), fn ($join) => $join
				->on(DB::raw('LEAST(m1.from_id, m1.to_id)'), '=', 'm2.from_id')
				->on(DB::raw('GREATEST(m1.from_id, m1.to_id)'), '=', 'm2.to_id')
				->on('m1.id', '=', 'm2.max_id'))
			->where(function($where) use($user_id) {
                $where->where('m1.from_id', $user_id)->orWhere('m1.to_id', $user_id);
            })
			->orderByDesc('m1.created_at', 'desc');

            if($chat_user_id) {
                $chat_list = $chat_list->where('m1.from_id', '!=', $chat_user_id)->where('m1.to_id', '!=', $chat_user_id);
            }
			// ->take($this->chat_per_page)
			// ->skip( (($page_no * $this->chat_per_page) - 1) )
			// ->get();
            
		
		$total_records = $chat_list->count();
		$chat_list = $chat_list->paginate($this->chat_per_page, ['*'], 'page', $page_no);

        $html = view('frontEnd.users.chat.loadChat', compact('chat_list', 'user_id', 'language_id', 'labels'))->render();

        $mainResult['statusCode'] = 200;
        $mainResult['total_records'] = $total_records;
        $mainResult['total_page'] = $chat_list->lastPage();
        $mainResult['html'] = $html;
        $mainResult['url'] = "";
        $mainResult['message'] = "";

        return response()->json($mainResult);
    }

    public function conversation(Request $request, $chat_user_id) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();

        $chat_user_id = decrypt($chat_user_id);
        $chatUser = MainUser::where('status', '=', 1)->find($chat_user_id);

        $chatUserConvo = UserConversation::where(function ($query) use ($user_id, $chat_user_id) {
			$query->where('from_id', '=', $user_id)
				  ->where('to_id', '=', $chat_user_id);
		})->orWhere(function ($query) use ($user_id, $chat_user_id) {
			$query->where('from_id', '=', $chat_user_id)
				  ->where('to_id', '=', $user_id);
		})->latest('id')->first();

        if($chatUser) {
            $PageTitle = $labels['chat_message'];
            $PageDescription = "";
            $PageKeywords = "";
            $WebmasterSettings = "";
            return view('frontEnd.users.chat.conversation', compact('language_id', 'labels', 'PageTitle', 'PageDescription', 'PageKeywords', 'WebmasterSettings', 'chatUser', 'chatUserConvo'));
        } 
        else {
            return redirect()->route('frontend.chat.list');
        }
    }

    public function fetchConversationList(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();
        $chat_user_id = $request->input('chat_user_id');
        $page_no = @$request->input('page_no') ?: 1;

        if($chat_user_id) {
            $chat_user_id = decrypt($chat_user_id);
        }
        
        $chatData = UserConversation::where(function ($query) use ($user_id, $chat_user_id) {
			$query->where('from_id', '=', $user_id)
				  ->where('to_id', '=', $chat_user_id);
		})->orWhere(function ($query) use ($user_id, $chat_user_id) {
			$query->where('from_id', '=', $chat_user_id)
				  ->where('to_id', '=', $user_id);
		});

		$total_records = $chatData->count();

		$chatData = $chatData->latest()->paginate($this->messages_per_page, ['*'], 'page', $page_no);

        $html = view('frontEnd.users.chat.loadCoversation', compact('chatData', 'user_id', 'language_id', 'labels'))->render();

        $mainResult['statusCode'] = 200;
        $mainResult['total_records'] = $total_records;
        $mainResult['total_page'] = $chatData->lastPage();
        $mainResult['html'] = $html;
        $mainResult['url'] = "";
        $mainResult['message'] = "";

        return response()->json($mainResult);
    }

    public function sendMessage(Request $request) {
        $language_id = Helper::currentLanguage()->id;
        $labels = Helper::LabelList($language_id);
        $user_id = Auth::guard('web')->id();
        $to_id = $request->input('to_id');
        $message = $request->input('message');

        if($message && $to_id) {

            $chat_user_id = decrypt($to_id);

			$conversation = new UserConversation;
			$conversation->from_id = $user_id;
			$conversation->to_id   = $chat_user_id;
			$conversation->message = $message;
			$conversation->read_status = 0;

			if($conversation->save()) {

				$response = [];
                $response['statusCode'] = 200;
				$response['message_id'] = (string) $conversation->id;
				$response['message'] = $conversation->message;
				$response['message_time'] = Helper::get_day_name($conversation->created_at);
                $response['url'] = "";
                $response['title'] = 'success';
                $response['text'] = 'success';

				return response()->json($response);
			}
			else{
				$response = [];
                $response['statusCode'] = 201;
                $response['url'] = "";
                $response['title'] = $labels['something_went_wrong'];
                $response['text'] = $labels['something_went_wrong'];

				return response()->json($response);
			}
		}
		else{
            $response = [];
            $response['statusCode'] = 203;
            $response['url'] = "";
            $response['title'] = $labels['enter_message'];
            $response['text'] = $labels['enter_message'];

            return response()->json($response);
		}
    }

    
}
