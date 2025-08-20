<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Auth;
use App\Models\TicketReply;
use App\Mail\SupportMailManager;
use Mail;

class SupportTicketController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_support_tickets'])->only('admin_index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('frontend.user.support_ticket.index', compact('tickets'));
    }

    public function admin_index(Request $request)
    {
        $sort_search = null;
        $tickets = Ticket::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $tickets = $tickets->where('code', 'like', '%' . $sort_search . '%');
        }
        $tickets = $tickets->paginate(15);
        return view('backend.support.support_tickets.index', compact('tickets', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd();
        $ticket = new Ticket;
        $ticket->code = strtotime(date('Y-m-d H:i:s')) . Auth::user()->id;
        $ticket->user_id = Auth::user()->id;
        $ticket->subject = $request->subject;
        $ticket->details = $request->details;
        $ticket->files = $request->attachments;

        if ($ticket->save()) {
            $this->send_support_mail_to_admin($ticket);
            flash(translate('Ticket has been sent successfully'))->success();
            return redirect()->route('support_ticket.index');
        } else {
            flash(translate('Something went wrong'))->error();
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
        } catch (\Exception $e) {}
    }

    public function send_support_reply_email_to_user($ticket, $tkt_reply)
    {
        $array['view'] = 'emails.support';
        // $array['subject'] = translate('Support ticket Code is') . ':- ' . $ticket->code;
        $array['subject'] = translate('Update on Your Complaint/Query with Shopeedo') . ':- ' . $ticket->code;

        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = 'Hi ' . htmlspecialchars($ticket->user->name) . ',
        <br>
        <p>We hope this message finds you well.</p>
        <br>
        <p>Thank you for reaching out to us regarding your recent experience with Shopeedo. We understand your concern and apologize for any inconvenience this situation may have caused.</p>
        <p>We wanted to update you on the status of your complaint/query:</p>
        <br>
        <strong>Complaint/Query Details:</strong>
        <br>
        <ul>
        <li><strong>Reference Number:</strong> ' . htmlspecialchars($ticket->code) . '</li>
        <li><strong>Date Submitted:</strong> ' . htmlspecialchars($ticket->created_at->format('Y-m-d')) . '</li>
        <li><strong>Subject:</strong> ' . htmlspecialchars($ticket->subject) . '</li>
        </ul>
        You have a new response for this complaint. Please check the complaint.
        <p><strong>Current Status: </strong> Our team is actively investigating the matter to ensure a prompt and satisfactory resolution. We are coordinating with the relevant departments and gathering all necessary information to address your concern effectively.</p>
        <p><strong>Next Steps: </strong> We aim to provide you with a comprehensive update within the next [X] business days. If additional information is needed from your side, we will reach out to you promptly. Please rest assured that we are prioritizing your case and working diligently to resolve the issue.</p>
        <p><strong>What You Can Expect:</strong></p>
        <p><strong>Refund Processing:</strong>
        If your complaint involves a refund, please note that the processing time can vary based on your payment method. We are actively processing your refund and will notify you once it has been completed.</p>
        <p><strong>Replacement or Reorder:</strong>
        If you are awaiting a replacement or wish to reorder, we will provide you with a dedicated support representative to assist you with the process. Please let us know how we can best accommodate your needs.</p>
        <p><strong>Further Communication:</strong>
         Our team may contact you for additional details or to provide you with an interim update. Your satisfaction is our top priority and we want to ensure all aspects of your complaint/query are thoroughly addressed.</p>
        <p><strong>Need Assistance?</strong>
         If you have any questions or require further assistance in the meantime, please do not hesitate to contact our Shopeedo Help Center. Our support team is available to help and provide any information you may need.</p>
        <p><strong>Important Reminder:</strong>
        Please ensure all transactions and communications are conducted directly through the Shopeedo platform for your safety and security. If you receive any requests for payment or information through unofficial channels, please report them to us immediately.</p>
        <p>We sincerely appreciate your patience and understanding as we work to resolve your concern. Thank you for giving us the opportunity to make things right.</p>
        <p><strong>Best regards,</strong></p>
        <p>The Shopeedo Support Team</p>';

        
        $array['link'] = $ticket->user->user_type == 'seller' ? route('seller.support_ticket.show', encrypt($ticket->id)) : route('support_ticket.show', encrypt($ticket->id));
        $array['sender'] = $tkt_reply->user->name;
        $array['details'] = $tkt_reply->reply;

        try {
            Mail::to($ticket->user->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {}
    }

    public function admin_store(Request $request)
    {
        $ticket_reply = new TicketReply;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = Auth::user()->id;
        $ticket_reply->reply = $request->reply;
        $ticket_reply->files = $request->attachments;
        $ticket_reply->ticket->client_viewed = 0;
        $ticket_reply->ticket->status = $request->status;
        $ticket_reply->ticket->save();


        if ($ticket_reply->save()) {

            flash(translate('Reply has been sent successfully'))->success();
            $this->send_support_reply_email_to_user($ticket_reply->ticket, $ticket_reply);
            return back();
        } else {
            flash(translate('Something went wrong'))->error();
        }
    }

    public function seller_store(Request $request)
    {
        $ticket_reply = new TicketReply;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = $request->user_id;
        $ticket_reply->reply = $request->reply;
        $ticket_reply->files = $request->attachments;
        $ticket_reply->ticket->viewed = 0;
        $ticket_reply->ticket->status = 'pending';
        $ticket_reply->ticket->save();
        if ($ticket_reply->save()) {

            flash(translate('Reply has been sent successfully'))->success();
            return back();
        } else {
            flash(translate('Something went wrong'))->error();
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
        $ticket = Ticket::findOrFail(decrypt($id));
        $ticket->client_viewed = 1;
        $ticket->save();
        $ticket_replies = $ticket->ticketreplies;
        return view('frontend.user.support_ticket.show', compact('ticket', 'ticket_replies'));
    }

    public function admin_show($id)
    {
        $ticket = Ticket::findOrFail(decrypt($id));
        $ticket->viewed = 1;
        $ticket->save();
        return view('backend.support.support_tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
