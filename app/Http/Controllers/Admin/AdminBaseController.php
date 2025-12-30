<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminBaseController extends Controller
{
    /**
     * @var array
     */
    public $data = [];
    public $allowedfileExtension = ['pdf', 'jpg', 'png', 'docx', 'xls', 'xlsx', 'csv', 'doc', 'txt', 'rtf', 'jpeg', 'gif', 'mp3'];
    public $CMMS_DB;
    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[ $name ]);
    }

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->middleware(function ($request, $next) {
            
            $publicPaths = [
           
            'imedical-parts',
            'public/path3',
        ];

        if (in_array($request->path(), $publicPaths)) {
            // Skip the condition for the specified public paths
            return $next($request);
        }
            
            $this->CMMS_DB = env('DB_DATABASE2', '');
            $this->user = auth()->user();
            
            if(null == $this->user->role()->first()){
                return redirect('/login');

            }

            return $next($request);
        });
    }

    public function generateTodoView()
    {
        $pendingTodos = $this->user->todoItems()->status('pending')->orderBy('position', 'DESC')->limit(5)->get();
        $completedTodos = $this->user->todoItems()->status('completed')->orderBy('position', 'DESC')->limit(5)->get();

        $view = view('sections.todo_items_list', compact('pendingTodos', 'completedTodos'))->render();

        return $view;
    }
    
    public function attach($request, $folder, $obj) {
        $attachment = '';
        $save = false;
        if ($request->hasFile('attachment')) {
            if ($obj->attachment_id == null) {
                $save = true;
                $attachment = \App\Attachment::create(['user_id' => user()->id, 'folder' => $folder]);
            } else {
                $attachment = \App\Attachment::findorfail($obj->attachment_id);
            }
            $attach_id = $attachment->id;
            foreach ($request->attachment as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $check = in_array(strtolower($extension), $this->allowedfileExtension);
                if (!$check) {
                    throw new \Exception('File extension is not alowed (' . $extension . ')');
                }
                $newfilename = \App\Helper\Files::uploadFile($file, $folder . '/');
                $attachment_file = new \App\AttachmentFile;
                $attachment_file->attachment_id = $attach_id;
                $attachment_file->filename = $newfilename;
                $attachment_file->extension = $extension;
                $attachment_file->original_name = $filename;
                $attachment_file->user_id = user()->id;
                $attachment_file->save();
            }

            if ($save) {
                $obj->attachment_id = $attachment->id;
                $obj->save();
            }
        }
        return $attachment;
    }
    
    public function filter($instance, $request) {
        foreach ($request->keys() as $field) {
            if (request($field) != '' && str_starts_with($field, 'filter_')) {
                $col = str_replace('filter_', '', $field);
                $col = str_replace('-', '.', $col);
                
                if (sizeof(explode(',',request($field))) > 1){
                    $instance->whereIn($col,explode(',',request($field)));
                }else{
                    $instance->where($col, request($field));
                }
            }
            if (request($field) != '' && str_starts_with($field, 'filterdate_')) {
                $col = str_replace('filterdate_', '', $field);
                //$col = str_replace('-', '.', $col);
                $instance->whereRaw("datediff(now(),$col) <= " . request($field));
            }
            if (request($field) != '' && str_starts_with($field, 'filterrange1')) {
                if (request('filterrange1_from') != null) {
                    $instance->where(DB::raw('DATE(' . request('filterrange1') . ')'), '>=', toCarbon(request('filterrange1_from'), true)->addDays(-1));
                }

                if (request('filterrange1_to') != null) {
                    $instance->where(DB::raw('DATE(' . request('filterrange1') . ')'), '<=', toCarbon(request('filterrange1_to'), true));
                }
                /*$instance->whereBetween(request('filterrange1'),
                        [toCarbon(request('filterrange1_from'), true), $todate]);*/
            }
        }
    }
    
    public function filterDate(Request $request,$query,$dateCol)
{
    $filterType = $request->input('filterType');

    switch ($filterType) {
        case 'range':
            $query->whereBetween($dateCol, [$request->fromDate, $request->toDate]);
            break;

        case 'year':
            $query->whereYear($dateCol, $request->year);
            break;

        case 'month':
            $query->whereYear($dateCol, $request->year)
                  ->whereMonth($dateCol, $request->month);
            break;

        case 'quarter':
            $startMonth = (($request->quarter - 1) * 3) + 1;
            $endMonth = $startMonth + 2;
            $query->whereYear($dateCol, $request->year)
                  ->whereBetween(\DB::raw('MONTH(.'.$dateCol.')'), [$startMonth, $endMonth]);
            break;
    }

    //$reports = $query->get();
    return $query;
}

}
