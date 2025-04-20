@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Users') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Users') }}</li>
@endsection
@php

    $logos = \App\Models\Utility::get_file('public/');

@endphp
@section('multiple-action-button')
@can('create-users')
<a href="{{ route('admin.users.create') }}"  class="mx-1" >
    <div class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('Create User') }}">
        <i class="ti ti-plus text-white"></i>
    </div>
</a>
@endcan
@if (\Auth::user()->parent == 0)
<a href="{{ route('userlog') }}" class="btn btn-sm btn-primary btn-icon" title="{{ __('User Login History') }}" data-bs-toggle="tooltip" data-bs-placement="top">
<i class="ti ti-user-check"></i>
</a>
@endif
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table id="pc-dt-simple" class="table">
                            <thead class="thead-light">
                            <tr>
                                {{-- <span class="hide-mob ms-2"> --}}
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('Picture') }}</th>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Email') }}</th>
                                    <th scope="col">{{ __('Category') }}</th>
                                    <th scope="col">{{ __('Role') }}</th>
                                    <th scope="col" class="text-end me-3">{{ __('Action') }}</th>
                                {{-- </span> --}}
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                    <tr>
                                        <th scope="row">{{++$index}}</th>
                                        <td>
                                            <a href="{{(!empty($user->avatar))? ($logos.$user->avatar): $logos.'avatar.png'}}" target="_blank">
                                                <img src="{{(!empty($user->avatar))? ($logos.$user->avatar): $logos.'avatar.png'}}" class="img-fluid rounded-circle card-avatar" width="35" id="blah3">
                                            </a>
                                        </td>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>
                                            @foreach ($user->categories as $category)
                                            <span class="badge badge-white p-2 px-3 rounded fix_badge" style="background: {{$category->color}}">{{$category->name}}</span>
                                            @endforeach
                                           </td>
                                        <td>
                                            <span class="badge bg-primary p-2 px-3 rounded rounded">
                                                {{ (!empty($user->parent) && $user->parent == 1) ? 'Agent' : 'Admin' }}
                                            </span>
                                        </td>
                                        <td class="text-end me-3">
                                            <div class="action-btn bg-warning ms-2">
                                                <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-size="md" data-url="{{ route('user.reset',\Crypt::encrypt($user->id))}}" data-ajax-popup="true" data-title="{{__('Reset Password')}}" data-toggle="tooltip" title="{{__('Reset Password')}}">
                                                    <span class="text-white">  <i class="ti ti-key"></i> </span>
                                                </a>
                                            </div>
                                            @can('edit-users')
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip"
                                                        title="{{ __('Edit') }}"> <span class="text-white"> <i class="ti ti-edit"></i></span></a>
                                                </div>
                                            @endcan
                                            @can('delete-users')
                                                <div class="action-btn bg-danger ms-2">
                                                    <form method="POST" action="{{ route('admin.users.destroy',$user->id) }}" id="delete-form-{{ $user->id }}">
                                                        @csrf
                                                        <input name="_method" type="hidden" value="DELETE">
                                                        <button type="submit" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-toggle="tooltip"
                                                        title="{{ __('Delete') }}">
                                                        <span class="text-white"> <i
                                                            class="ti ti-trash"></i></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endcan

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
