<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SaunaController extends Controller
{
  // 一覧表示
  public function index(){
    return Inertia::render('Sauna/Index');
  }

  // 新規作成表示
  public function create(){
    return Inertia::render('Sauna/Create');
  }

  // 新規作成処理
  public function store(Request $request){
    return redirect()->route('saunas.index');
  }

  // 詳細表示
  public function show($id){
    return Inertia::render('Sauna/Show');
  }

  // 詳細編集画面表示
  public function edit($id){
    return Inertia::render('Sauna/Edit');
  }

  // 更新処理
  public function update(Request $request, $id){
    return redirect()->route('saunas.show', $id);
  }

  // 削除
  public function destroy($id){
    return redirect()->route('saunas.index');
  }
}

