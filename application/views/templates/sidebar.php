<aside class="aside">
    <div class="aside-inner">
        <nav data-sidebar-anyclick-close="" class="sidebar" style="padding-bottom: 80px;">
            <ul class="nav">
                <li class="has-user-block">
                    <div id="user-block" class="collapse">
                        <div class="item user-block">
                            <div class="user-block-picture">
                                <div class="user-block-status">
                                    <img src="<?= base_url('src/img/avatar/' . $user['avatar']); ?>" alt="Avatar" width="60" height="60"
                                        class="img-thumbnail img-circle">
                                    <div class="circle circle-success circle-lg"></div>
                                </div>
                            </div>
                            <div class="user-block-info">
                                <span class="user-block-name"><?= $user['username']; ?></span>
                                <span class="user-block-role"></span>
                            </div>
                        </div>
                    </div>
                </li>

                <?php
                    $on_session = $this->db->get_where('users', [ 'username' => $_GET['username'] ])->row_array();
                    $role_id = $on_session['role_id'];
                    $queryMenu = "SELECT `user_menu`.`id`, `menu` 
                                    FROM `user_menu` 
                                    JOIN `user_access_menu` 
                                      ON `user_menu`.`id` = `user_access_menu`.`menu_id` 
                                   WHERE `user_access_menu`.`role_id` = $role_id
                                ORDER BY `user_access_menu`.`menu_id` ASC
                                ";
                    $menu = $this->db->query($queryMenu)->result_array();
                ?>
                
                <!-- ngRepeat: Menu -->
                <?php foreach($menu as $m):?>
                <li class="nav-heading ">
                    <span><?= $m['menu']; ?></span>
                </li>

                    <?php
                        $querySubMenu = "SELECT * 
                                        FROM `user_sub_menu`
                                        WHERE `user_sub_menu`.`menu_id` = {$m['id']}
                                        AND `user_sub_menu`.`is_active` = 1
                                        ";
                        $subMenu = $this->db->query($querySubMenu)->result_array();
                    ?>

                    <!-- ngRepeat: SubMenu -->
                    <?php foreach($subMenu as $sm):?>
                    <li class="<?= menu_is_active($sm['name'], $title) ?>">
                        <a 
                            class="js-navlink" 
                            href="<?= base_url($sm['url']); ?>" 
                            title="<?= $sm['name']; ?>"
                        >
                            <em 
                                class="<?= $sm['icon']; ?>"
                            ></em>
                            <span>
                                <?= $sm['name']; ?>
                            </span>
                        </a>
                    </li>
                    <?php endforeach;?>
                    <!-- End ngRepeat: SubMenu -->
                    
                <?php endforeach;?>
                <!-- End ngRepeat: Menu -->

                
            </ul>
        </nav>
    </div>
</aside>