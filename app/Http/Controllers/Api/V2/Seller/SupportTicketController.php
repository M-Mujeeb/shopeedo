<?php

namespace App\Http\Controllers\Api\V2\Seller;


use App\Mail\SupportMailManager;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Auth;
use Mail;
use Carbon\Carbon;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(9);
        $user = User::where('id', auth()->user()->id)->first();

        $ticketsData = $tickets->map(function ($ticket) {


            return [
                'id' => $ticket->id,
                'ticket_id' => $ticket->code,
                'sending_date' => $ticket->created_at->format('Y-m-d H:i:s'),
                'subject' => $ticket->subject,
                'desc' => $ticket->details,
                'status' => $ticket->status,
                'file' => $ticket->files ?  uploaded_asset($ticket->files): "",
                


            ];
        });
        return response()->json([
            'result' => true,
            'message' => 'All tickets',
            'data' => [
                'tickets' => $ticketsData,
                'pagination' => [
                    'total' => $tickets->total(),
                    'per_page' => $tickets->perPage(),
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                ],
                'avatar' => uploaded_asset($user->avatar_original),
                'user_name' => $user->name
            ],


        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|max:191',
            'details' => 'required',
        ]);

        $ticket = new Ticket;

        do {
            $code = mt_rand(1000000000, 9999999999) . date('s');  
        } while (Ticket::where('code', $code)->exists());


         // $ticket->code = max(100000, (Ticket::latest()->first() != null ? Ticket::latest()->first()->code + 1 : 0)) . date('s');
        $ticket->code = $code;
        $ticket->user_id = $request->user_id;
        $ticket->subject = $request->subject;
        $ticket->details = $request->details;
        $ticket->files = $request->attachments;

        if ($ticket->save()) {
            $this->send_support_mail_to_admin($ticket);
            return response()->json([
                'result' => true,
                'message' => 'Ticket has been sent successfully',
            ]);
        } else {
            return response()->json([
                'result' => false,
                'message' => 'Something went wrong!',
            ]);
        }
    }

    public function send_support_mail_to_admin($ticket)
    {
        $array['view'] = 'emails.support';
        $array['subject'] = translate('Support ticket Code is') . ':- ' . $ticket->code;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = translate('Hi. A ticket has been created. Please check the ticket.');
        $array['link'] = route('support_ticket.admin_show', encrypt($ticket->id));
        $array['sender'] = $ticket->user->name;
        $array['details'] = $ticket->details;

        try {
            Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {
            // dd($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
        $all_tickets_users = collect();
        $ticket = Ticket::findOrFail($id);
        $ticket->client_viewed = 1;
        $ticket->save();
        $ticket_replies = $ticket->ticketreplies;
        // dd($ticket_replies);
        $counter = 0;
        foreach ($ticket_replies as $ticketreply) {
            $user = User::where('id', $ticketreply->user_id)->first();
            $reply = $ticketreply->reply;

            $all_tickets_users->push([
                'user_type' => $user->user_type,
                'reply' => $reply,
                'user_name' => $user->name,
                'created_at' => $ticketreply->created_at->format('Y-m-d H:i:s'),
                'files' => $ticketreply->files ?  uploaded_asset($ticketreply->files): "",
                'avatar' => uploaded_asset($user->avatar_original),
            ]);
        }

        return response()->json([
            'result' => true,
            'message' => 'Ticket Details',
            'data' => [
                // 'tickets' => $ticket,
                // 'ticket_replies' => $ticket_replies,
                'ticket_replies' => $all_tickets_users,

            
            ],
            
        ]); 
    }


    public function ticket_reply_store(Request $request)
    {
        $ticket_reply = new TicketReply;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = $request->user_id;
        $ticket_reply->reply = $request->reply;
        $ticket_reply->files = $request->attachments;

       
        $ticket = $ticket_reply->ticket()->first(); 

        if ($ticket) {
            $ticket->viewed = 0;
            $ticket->status = 'pending';
            $ticket->save();
        }

        if ($ticket_reply->save()) {
            return response()->json([
                'result' => true,
                'message' => 'Ticket Details',
                'data' => [
                    'user_type' => "seller",
                    'reply' => $ticket_reply->reply,
                    'created_at' => Carbon::now(),
                    'files' => uploaded_asset($ticket_reply->files),
                ],
            ]);
        } else {
            return response()->json([
                'result' => false,
                'message' => 'Ticket Details',
                'data' => [
                    'error' => 'Error in saving ticket reply',
                ],
            ]);
        }
    }
}
