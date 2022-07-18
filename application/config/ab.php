<div id="navbar" class="navbar-collapse collapse navigation-holder">
                <button class="close-navbar"><i class="ti-close"></i></button>
                <ul class="nav navbar-nav">
                     <?php foreach($cat_data as $cat_data_key => $cat_data_row) {
                        if(!empty($cat_data_row['menu_function_name'])){
                            $url = base_url().'Frontend/'.$cat_data_row['menu_function_name'];
                        }else{
                            $url = "javascript:void(0)";
                        }
                      ?>
                    <li>
                        <a href="<?= $url?>"><?=$cat_data_row['menu'];?></a>
                        <ul class="sub-menu">
                            <?php
                                $dump_key = 0;
                                foreach($cat_data_row['child_name'] as $key1 => $ch) {
                                    $dump_key = $dump_key+1;
                                    $sub_id=explode("_",$key1);
                                        if (@$sub_id[2]==1) {
                                            if(!empty($cat_data_row['child_name'][$key1])){
                                                $class_name ='menu-item-has-children';
                                            }else{
                                                $class_name ="";
                                            }
                            ?>
                            <li class="<?=$class_name?>">
                                <?php if ($dump_key==1) { ?>
                                <?php } ?>
                                <a href="<?= base_url();?>Frontend/about"><?=$sub_id[0];?></a>
                                <ul class="sub-menu">
                                     <?php foreach($cat_data_row['child_name'][$key1] as $s){?>
                                    <li class="menu-item-has-children"><a href="<?= base_url();?>Frontend/service_Roll_Chocking"><?=$s['child_menu_name'];?></a>
                                    </li>
                                <?php }?>
                                </ul>
                            </li>
                        <?php }}?>
                        </ul>
                    </li>
                <?php }?>
                </ul>
            </div>