<?php

namespace App\Http\Controllers;

use App\Code;
use App\File;
use App\Http\Requests\MemberRequest;
use App\Member;
use App\MemberProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{

    public function index(Request $request, $page = 0, $rowsPerPage = 10, $keyword = null) {

        $schools = Code::getCodes(Code::SCHOOL_CODE_ID);
        $userStatuses = User::STATUS_ITEMS;

        if ($request->ajax()) {
            //DB::enableQueryLog();
            $query = Member::addSelect(['school_text' => Code::select('name_ko')->whereColumn('id', 'members.school_id')->limit(1)]);
            if ($keyword) {
                $query->where(function($query) use($keyword){
                    $query->where('name', 'like', '%'.$keyword.'%');
                    $query->orWhere('mobile', 'like', '%'.$keyword.'%');
                });
            }

            $result['total'] = $query->count();
            $query->orderBy('id', 'desc');
            if ($rowsPerPage > 0) {
                $offset = ($page - 1) * $rowsPerPage;
                $query->offset($offset)->limit($rowsPerPage);
            }
            $result['items'] = $query->get();
            //dd(DB::getQueryLog());
            return response()->json($result);
        }

        return view('member.index', compact('schools', 'userStatuses'));
    }

    public function store(MemberRequest $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $result = false;

                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                if (isset($request->password)) {
                    $user->password = Hash::make($request->password);
                }
                $user->level = Member::LEVEL;
                $user->status = User::STATUS_REGISTER;
                if ( $user->save() ) {
                    $memberProfile = new MemberProfile();
                    $memberProfile->id               = $user->id;
                    $memberProfile->cardinal_numeral = $request->cardinal_numeral;
                    $memberProfile->school_id        = $request->school_id;
                    $memberProfile->note             = $request->note;
                    $memberProfile->company          = $request->company;
                    $memberProfile->part             = $request->part;
                    $memberProfile->duty             = $request->duty;
                    $memberProfile->mobile           = $request->mobile;
                    $memberProfile->url              = $request->url;
                    if ($request->member_photo_id > 0) {
                        $file = File::findOrFail($request->member_photo_id);
                        $file->source_id = $user->id;
                        $file->save();
                    }
                    if ($request->member_business_card_id > 0) {
                        $file = File::findOrFail($request->member_business_card_id);
                        $file->source_id = $user->id;
                        $file->save();
                    }
                    $result = $memberProfile->save();
                }

                return $result;
            });

        } catch (\Exception $e) {
            return response()->json($e, 500);
        }

        return response()->json($result, 200);
    }

    public function show($id = null)
    {
        //TODO bug fix
        //$user = Auth::guard('api')->user(['api_token'=>$request->api_token]);
        $user = Auth::guard('web')->user();
        $user = Auth::guard('api')->user();
        //print_r($user->name);
        $user = Auth::user();
        //$user = Auth::guard('api')->user()->status;
        //print_r($user->name);
        //exit;

        $id = ($id) ? $id : Auth::user()->id;
        //DB::enableQueryLog();
        $member = Member::findOrFail($id);
        //dd(DB::getQueryLog());
        return response()->json($member, 200);
    }

    public function update(MemberRequest $request, $id = null)
    {
        $id = ($id) ? $id : Auth::user()->id;

        try {
            $result = DB::transaction(function () use ($id, $request) {
                $result = false;

                $user = User::findOrFail($id);
                $user->name = $request->name;
                $user->email = $request->email;
                if (isset($request->password)) {
                    $user->password = Hash::make($request->password);
                }
                $user->status = $request->status;
                if ( $user->save() ) {
                    $memberProfile = MemberProfile::findOrFail($id);
                    $memberProfile->cardinal_numeral = $request->cardinal_numeral;
                    $memberProfile->school_id        = $request->school_id;
                    $memberProfile->note             = $request->note;
                    $memberProfile->company          = $request->company;
                    $memberProfile->part             = $request->part;
                    $memberProfile->duty             = $request->duty;
                    $memberProfile->mobile           = $request->mobile;
                    $memberProfile->url              = $request->url;
                    if ($request->member_photo_id > 0) {
                        $file = File::findOrFail($request->member_photo_id);
                        $file->source_id = $user->id;
                        $file->save();
                    }
                    if ($request->member_business_card_id > 0) {
                        $file = File::findOrFail($request->member_business_card_id);
                        $file->source_id = $user->id;
                        $file->save();
                    }
                    $result = $memberProfile->save();
                }

                return $result;
            });

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($result);
    }

    public function destroy($id) {

        try {
            $result = DB::transaction(function () use ($id) {
                //delete file
                $oFiles = File::whereIn('source_type',[File::SOURCE_TYPE_MEMBER_PHOTO, File::SOURCE_TYPE_MEMBER_BUSINESS_CARD])->where('source_id', $id);
                $files = $oFiles->get();
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file->path);
                }
                $oFiles->delete();

                //delete profile
                if ( MemberProfile::destroy($id) ) {
                    //delete login info
                    return User::destroy($id);
                }
            });
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }

        return response()->json($result, 200);
    }

    public function storeFile(Request $request, $type) {
        try {
            $file = $request->file('file');
            $path = Storage::disk('public')->put('members', $file);

            $dbFile = new File();
            $dbFile->source_type = $type;
            $dbFile->source_id = $request->id;
            $dbFile->name = $file->getClientOriginalName();
            $dbFile->path = $path;
            $dbFile->size = $file->getSize();
            $dbFile->type = $file->getClientMimeType();
            $dbFile->save();

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(['type' => File::SOURCE_TYPES[$type], 'id' => $dbFile->id], 200);
    }

    public function destroyFile($type, $id) {
        try {
            $file = File::findOrFail($id);
            Storage::disk('public')->delete($file->path);
            $file->delete();
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }

        return response()->json($file->id, 200);
    }

    public function applyStatus(Request $request) {
        try {
            $result = Member::whereIn('id', $request->toArray())->update(['status' => User::STATUS_NORMAL]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($result);
    }

}
