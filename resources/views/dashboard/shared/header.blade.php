
      
    <div class="c-wrapper">
      <header class="c-header c-header-light c-header-fixed c-header-with-subheader">
        <button class="c-header-toggler c-class-toggler d-lg-none mr-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show"><span class="c-header-toggler-icon"></span></button><a class="c-header-brand d-sm-none" href="#"><img class="c-header-brand" src="{{ url('/assets/brand/coreui-base.svg" width="97" height="46" alt="CoreUI Logo"></a>
        <button class="c-header-toggler c-class-toggler ml-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true"><span class="c-header-toggler-icon"></span></button>
        <?php
            use App\MenuBuilder\FreelyPositionedMenus;
            if(isset($appMenus['top menu'])){
                FreelyPositionedMenus::render( $appMenus['top menu'] , 'c-header-', 'd-md-down-none');
            }
        ?>  
        <ul class="c-header-nav ml-auto mr-4">
          @if(!empty(auth()->user()))
            <li class="c-header-nav-item dropdown">
              <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bell"></i>
                    <span class="badge badge-light bg-success badge-xs">{{auth()->user()->unreadNotifications->count()}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                    <span class="dropdown-item-text">You have {{auth()->user()->unreadNotifications->count()}} new notifications</span>
                   
                    <div class="dropdown-divider"></div>
                    @foreach (auth()->user()->unreadNotifications->take(10) as $notification)
                        <a class="dropdown-item">
                          <p style="color:green;">
                            {{substr($notification->data['data'], 0, 40)}}
                            @if(strlen($notification->data['data']) > 40) ....@endif
                          </p>
                        </a> 
                    @endforeach
                    @if(auth()->user()->unreadNotifications->count() < 10)
                      @foreach (auth()->user()->readNotifications->take( 10 - auth()->user()->unreadNotifications->count()) as $notification)
                        <a class="dropdown-item">
                          <p>
                            {{substr($notification->data['data'], 0, 40)}}
                            @if(strlen($notification->data['data']) > 40) ....@endif
                          </p>
                        </a> 
                      @endforeach
                    @endif
                    <div class="dropdown-divider"></div>
                    @if (auth()->user()->unreadNotifications)
                      <a class="dropdown-item" href="{{route('mark-as-read')}}" style="color:rgb(0, 98, 128);">Mark All as Read</a>
                    @endif
                </div>
            </li>
          @endif
          <li class="c-header-nav-item d-md-down-none mx-2"><a class="c-header-nav-link">
              <svg class="c-icon">
                <use xlink:href="{{ url('/icons/sprites/free.svg#cil-list-rich') }}"></use>
              </svg></a></li>
          <li class="c-header-nav-item d-md-down-none mx-2"><a class="c-header-nav-link">
              <svg class="c-icon">
                <use xlink:href="{{ url('/icons/sprites/free.svg#cil-envelope-open') }}"></use>
              </svg></a></li>
          <li class="c-header-nav-item dropdown">
            <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
              <div class="c-avatar"><img class="c-avatar-img" src="{{ url('/assets/img/avatars/6.jpg') }}" alt="user@email.com"></div>
            </a>
            <div class="dropdown-menu dropdown-menu-right pt-0">
              <div class="dropdown-header bg-light py-2"><strong>Account</strong></div><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-bell') }}"></use>
                </svg> Updates<span class="badge badge-info ml-auto">42</span></a><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-envelope-open') }}"></use>
                </svg> Messages<span class="badge badge-success ml-auto">42</span></a><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-task') }}"></use>
                </svg> Tasks<span class="badge badge-danger ml-auto">42</span></a><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-comment-square') }}"></use>
                </svg> Comments<span class="badge badge-warning ml-auto">42</span></a>
              <div class="dropdown-header bg-light py-2"><strong>Settings</strong></div><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-user') }}"></use>
                </svg> Profile</a><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-settings') }}"></use>
                </svg> Settings</a><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-credit-card') }}"></use>
                </svg> Payments<span class="badge badge-secondary ml-auto">42</span></a><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-file') }}"></use>
                </svg> Projects<span class="badge badge-primary ml-auto">42</span></a>
              <div class="dropdown-divider"></div><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-lock-locked') }}"></use>
                </svg> Lock Account</a><a class="dropdown-item" href="#">
                <svg class="c-icon mr-2">
                  <use xlink:href="{{ url('/icons/sprites/free.svg#cil-account-logout') }}"></use>
                </svg><form action="{{ url('/logout') }}" method="POST"> @csrf <button type="submit" class="btn btn-ghost-dark btn-block">Logout</button></form></a>
            </div>
          </li>
        </ul>
        <div class="c-subheader px-3">
          <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <?php $segments = ''; ?>
            @for($i = 1; $i <= count(Request::segments()); $i++)
                <?php $segments .= '/'. Request::segment($i); ?>
                @if($i < count(Request::segments()))
                    <li class="breadcrumb-item">{{ Request::segment($i) }}</li>
                @else
                    <li class="breadcrumb-item active">{{ Request::segment($i) }}</li>
                @endif
            @endfor
          </ol>
        </div>
    </header>