@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">产品管理</div>
                <div class="panel-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <a href="{{ url('admin/trade/create') }}" class="btn btn-lg btn-primary">添加新产品</a>
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>订单号</th>
                          <th>姓名</th>
                          <th>旺旺号</th>
                          <th>手机号</th>
                          <th>操作</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($clients as $client)
                        <tr>
                          <td>{{ $client->id }}</td>
                          <td><a href="{{ url('trade/'.$client->order_id) }}" target="_blank">{{ $client->order_id }}</td>
                          <td>{{ $client->shipping_consignee }}</a></td>
                          <td><a href="{{ url($client->buyer_indexpage) }}" target="_blank">{{ $client->buyer_wangwang }}</a></td>

                          @if ($client->shipping_mobilephone) 
                          <td>{{ $client->shipping_mobilephone }}</td>
                          @else 
                          <td>{{ $client->shipping_telephone }}</td>
                          @endif
                          <td>
                            <a href="{{ url('admin/products/'.$client->id.'/edit') }}" class="btn btn-success">编辑</a>
                            <form action="{{ url('admin/products/'.$client->id) }}" method="POST" style="display: inline;">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger">删除</button>
                        </form></td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>

                    <!-- 分页 -->
                    <div class="text-center">{{ $clients->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection