<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Crm;

class AlicrmController extends Controller
{
    //首页
    public function index()
	{
    	return view('admin/crm/index')->withClients(Crm::paginate(30));
	}
	
	//新增
    public function create()
	{
    	return view('admin/jingdong/create');
	}

	//存储
	public function store(Request $request)
	{
		$this->validate($request, [
    		'name' => 'required',
    		'skuid' => 'required|unique:jingdongs|max:16',
    		'email' => 'required',
    		'rate' => 'required',
    		'type' => 'required'
    	]);

    	$task = new Jingdong;
    	$task->type = $request->get('type');
    	$task->name = $request->get('name');
    	$task->skuid = $request->get('skuid');
    	$task->current_price = $request->get('current_price');
    	$task->target_price = $request->get('target_price');
    	$task->email = $request->get('email');
    	$task->phone = $request->get('phone');
    	$task->rate = $request->get('rate');
    	// $task->times = $request->get('times');

    	if ($task->save()) {
        	return redirect('admin/jingdong');
    	} else {
        	return redirect()->back()->withInput()->withErrors('保存失败！');
    	}
	}

	//编辑
	public function edit($id)
	{
		return view('admin/jingdong/edit')->withProducts(Jingdong::find($id));
	}

	//更新
	public function update(Request $request,$id)
	{
        $this->validate($request, [
            'name' => 'required|max:20',
            'skuid' => 'required|max:10',
            'rate' => 'required',
            'email' => 'required',
        ]);

        $jd = Jingdong::find($id);
        if ($request->get('type') === 0) {
            $jd->name = $request->get('name');
            $jd->skuid = $request->get('skuid');
            $jd->type = $request->get('type');
            $jd->current_price = $request->get('current_price');
            $jd->target_price = $request->get('target_price');
            $jd->rate = $request->get('rate');
            $jd->email = $request->get('email');
            $jd->phone = $request->get('phone');
            $jd->status = $request->get('status');
        } else {
            $jd->name = $request->get('name');
            $jd->skuid = $request->get('skuid');
            $jd->type = $request->get('type');
            $jd->rate = $request->get('rate');
            $jd->email = $request->get('email');
            $jd->phone = $request->get('phone');
            $jd->status = $request->get('status');
        }
        // 保存
        if ($jd->save()) {
            return Redirect::to('admin/jingdong');
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }

	}

	// 删除
	public function destroy()
	{

	}
}
