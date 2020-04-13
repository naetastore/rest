<header class="topnavbar-wrapper">
    <nav role="navigation" class="navbar topnavbar">
        <div class="navbar-header">
            <a href="<?= base_url('admin/base'); ?>" class="js-navlink navbar-brand">
            <div class="brand-logo">
                <div class="h4">Naeta Rest</div>
            </div>
                <div class="brand-logo-collapsed">
                    <div class="h4">Naeta</div>
                </div>
            </a>
        </div>
        <div class="nav-wrapper">
            <ul class="nav navbar-nav">
                <li>
                    <a href="#" data-trigger-resize="" data-toggle-state="aside-collapsed" class="hidden-xs">
                        <em class="fa fa-navicon"></em>
                    </a>
                    <a href="#" data-toggle-state="aside-toggled" data-no-persist="true"
                        class="visible-xs sidebar-toggle">
                        <!-- <em class="fa fa-navicon"></em> -->
                        Menu
                    </a>
                </li>
                <li>
                    <a id="user-block-toggle" href="#user-block" data-toggle="collapse">
                        <em class="icon-user"></em>
                    </a>
                </li>
                <li>
                    <a href="lock.html" title="Lock screen">
                        <em class="icon-lock"></em>
                    </a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="#" data-search-open="">
                        <em class="icon-magnifier"></em>
                    </a>
                </li>
                <li class="dropdown dropdown-list">
                    <a href="#" data-toggle="dropdown">
                        <em class="icon-bell"></em>
                        <div class="label label-danger"></div>
                    </a>
                    <ul class="dropdown-menu animated flipInX">
                        <li>
                            <div class="list-group">
                                <a href="#" class="list-group-item">
                                    <div class="media-box">
                                        <div class="media-box-body clearfix">
                                            <p class="m0">New followers</p>
                                            <p class="m0 text-muted">
                                                <small>1 new follower</small>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="list-group-item">
                                    <small>More notifications</small>
                                    <span class="label label-danger pull-right">14</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" data-toggle-state="offsidebar-open" data-no-persist="true">
                        <em class="icon-notebook"></em>
                    </a>
                </li>
            </ul>
        </div>
        <form role="search" action="search.html" class="navbar-form">
            <div class="form-group has-feedback">
                <input type="text" placeholder="Type and hit enter ..." class="form-control">
                <div data-search-dismiss="" class="fa fa-times form-control-feedback"></div>
            </div>
            <button type="submit" class="hidden btn btn-default">Submit</button>
        </form>
    </nav>
</header>