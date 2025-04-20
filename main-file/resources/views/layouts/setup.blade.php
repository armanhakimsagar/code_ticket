
<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        <a href="{{route('admin.category')}}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'admin.category' ) ? ' active' : '' }}">{{__('Category')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('admin.group') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'admin.group' ) ? 'active' : '' }}">{{__('Group')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('admin.operating_hours.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'admin.operating_hours.index' ) ? 'active' : '' }}">{{__('Operating Hours')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('admin.priority.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'admin.priority.index' ) ? 'active' : '' }}">{{__('Priority')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('admin.policiy.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'admin.policiy.index' ) ? 'active' : '' }}">{{__('SLA Policy Setting')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

    </div>
</div>

