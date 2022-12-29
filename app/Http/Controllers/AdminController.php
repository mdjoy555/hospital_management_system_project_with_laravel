<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Notifications\SendMailNotification;
use Notification;
class AdminController extends Controller
{
    public function addview()
    {
        if(Auth::id())
        {
            if(Auth::user()->usertype==1)
            {
                return view('admin.add_doctor');
            }
            else
            {
                return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }
    }

    public function upload(Request $request)
    {
        $doctor = new doctor;
        $image = $request->image;
        $imagename = time().'.'.$image->getClientOriginalExtension();
        $request->image->move('doctorimage', $imagename);
        $doctor->image = $imagename;
        $doctor->name = $request->name;
        $doctor->phone = $request->number;
        $doctor->room = $request->room;
        $doctor->speciality = $request->speciality;

        $doctor->save();

        return redirect()->back()->with('message','Doctor Added Successfully');
    }

    public function showappointment()
    {
        if(Auth::id())
        {
            if(Auth::user()->usertype==1)
            {
                $data = appointment::all();

                return view('admin.showappointment',compact('data'));
            }
            else
            {
                return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }
    }

    public function approve($id)
    {
        $data = appointment::find($id);
        $data->status = 'Approved';
        $data->save();

        return redirect()->back();
    }

    public function cancel($id)
    {
        $data = appointment::find($id);
        $data->status = 'Canceled';
        $data->save();

        return redirect()->back();
    }

    public function showdoctor()
    {
        if(Auth::id())
        {
            if(Auth::user()->usertype==1)
            {
                $data = doctor::all();

                return view('admin.showdoctor', compact('data'));
            }
            else
            {
                return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }
    }

    public function editdoctor($id)
    {
        if(Auth::id())
        {
            if(Auth::user()->usertype==1)
            {
                $data = doctor::find($id);

                return view('admin.editdoctor', compact('data'));
            }
            else
            {
                return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }
    }

    public function updatedoctor(Request $request,$id)
    {
        $doctor = doctor::find($id);
        $doctor->name = $request->name;
        $doctor->phone = $request->phone;
        $doctor->speciality = $request->speciality;
        $doctor->room = $request->room;
        $image = $request->image;
        if($image)
        {
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $request->image->move('doctorimage', $imagename);
            $doctor->image = $imagename;
        }
        $doctor->save();

        return redirect()->back()->with('message','Doctor Updated Successfully');
    }

    public function deletedoctor($id)
    {
        $data = doctor::find($id);
        $data->delete();

        return redirect()->back();
    }

    public function mail_view($id)
    {
        if(Auth::id())
        {
            if(Auth::user()->usertype==1)
            {
                $data = appointment::find($id);

                return view('admin.mail_view',compact('data'));
            }
            else
            {
                return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }
    }

    public function sendmail(Request $request, $id)
    {
        $data = appointment::find($id);
        $details = [
            'greeting' => $request->greeting,
            'body' => $request->body,
            'actiontext' => $request->actiontext,
            'actonurl' => $request->actionurl,
            'endpart' => $request->endpart
        ];
        Notification::send($data, new SendMailNotification($details));

        return redirect()->back()->with('message','Mail send successfully');
    }
}
