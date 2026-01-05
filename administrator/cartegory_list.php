
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Danh sách thương hiệu</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Thương hiệu</th>
                    <th scope="col">Logo</th>
                    <th scope="col" class="text-center">Sửa</th>
                    <th scope="col" class="text-center">Ẩn</th>
                </tr>
                <?php
                    $get_cartegory = "select * from cartegory ORDER BY cartegory_id DESC";
                    $run_cartegory = mysqli_query($conn, $get_cartegory);
                    while($row_cartegory = mysqli_fetch_array($run_cartegory)){
                        $cartegory_id = $row_cartegory['cartegory_id'];
                            $cartegory_name = $row_cartegory['cartegory_name'];
                            $cartegory_img = $row_cartegory['cartegory_img'];
                            $cartegory_status = isset($row_cartegory['cartegory_status']) ? $row_cartegory['cartegory_status'] : 'visible';
                    
                ?>
                <tr>
                    <th scope="row"><?php echo $cartegory_id; ?></th>
                    <td><?php echo $cartegory_name ?></td>
                    <td><img src="cartegory_img/<?php echo $cartegory_img ?>" width="80px" alt=""></td>
                    <td class="text-center text-primary"><a href="index.php?cartegory_edit=<?php echo $cartegory_id; ?>" class="text-primary"><i class="fas fa-edit"></i></td>
                    <td class="text-center">
                    <?php if($cartegory_status === 'hidden'){ ?>
                        <button style="border: none; background-color: transparent;" onclick="unhide_cartegory(<?php echo $cartegory_id; ?>)" class="text-success"><i class="fas fa-eye"></i></button>
                    <?php } else { ?>
                        <button style="border: none; background-color: transparent;" onclick="hide_cartegory(<?php echo $cartegory_id; ?>)" class="text-warning"><i class="fas fa-eye-slash"></i></button>
                    <?php } ?>
                    </td>
                </tr>
                <?php
                    }
                ?>

            <script>
            function hide_cartegory(id){
                var result = confirm("Khi ẩn thương hiệu, các sản phẩm của thương hiệu sẽ bị ẩn (không hiển thị cho người dùng). Bạn chắc chắn muốn ẩn? ");
                if(result==true){
                    document.location = 'index.php?cartegory_hide='+id;
                }
            }
            function unhide_cartegory(id){
                var result = confirm("Bạn muốn hiện lại thương hiệu này và các sản phẩm liên quan? ");
                if(result==true){
                    document.location = 'index.php?cartegory_unhide='+id;
                }
            }
            </script>
                </table>
            </div>




