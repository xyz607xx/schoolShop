<?php
    include_once('../pdoLink.php');
    if(empty($_SESSION))
        session_start();
    $user = $_SESSION['loginUser'];
    $sql = 'select Distinct(o_code) from order_table';
    $select = $db->prepare($sql);
    $select->execute();
    while ($code = $select->fetch(PDO::FETCH_ASSOC)['o_code']){
        ?>
        <table style="width:100%;text-align:center;">
            <th>狀態</th>
            <th>訂單編號</th>
            <th>詳細</th>
            <tr>
                <td>
                    <?php 
                        $sql = 'select * from order_status where o_code = :code';
                        $orderStatus = $db->prepare($sql);
                        $orderStatus->bindValue(':code',$code);
                        $orderStatus->execute();
                        $orderStatus = $orderStatus->fetch(PDO::FETCH_ASSOC);
                        echo $orderStatus['o_status'];
                        if($orderStatus['o_pay']=='T'){ ?>
                            <div style="color:blue">已付款</div>
                        <?php }else{ ?>
                            <div style="color:red;" class="payDiv<?php echo $code;?>">未付款</div>
                        <?php }
                    ?>
                </td>
                <td><?php echo $code;?></td>
                <td>
                    <button type="button" class="btn btn-success orderRecordBtn" va="<?php echo $code;?>">詳細/修改</button>
                </td>
            </tr>
        </table>
        <div style="height:100vh;" class="modal fade" id="orderRecord<?php echo $code;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close"  aria-label="Close"  data-dismiss="modal">
                            <span aria-hidden="true" va="<?php echo $code;?>" class="closeOrder">&times;</span>
                        </button>
                        <h4 class="modal-title">訂單編號 <?php echo $code;?></h4>
                    </div>
                    <div class="modal-body">
                        <table id="orderTable" style="width:100%;text-align:center;">
                        <th>品項</th>
                        <th>種類</th>
                        <th>價錢</th>
                        <th>數量</th>
                        <th style="text-align:right;">合計</th>
                        <?php
                            $sql = 'select * from order_table where o_code = :code';
                            $time = '';
                            $orderSelect = $db->prepare($sql);
                            $orderSelect->bindValue(':code',$code);
                            $orderSelect->execute();
                            $sql = 'select * from order_status where o_code = :code';
                            $status = $db->prepare($sql);
                            $status->bindValue(":code",$code);
                            $status->execute();
                            $statusObj = $status->fetch(PDO::FETCH_ASSOC);
                            while($order = $orderSelect->fetch(PDO::FETCH_ASSOC)){
                                $time = $order['o_reserve'];
                        ?>
                        <tr>
                            <td><?php echo $order['o_item'];?></td>
                            <td><?php echo $order['o_other'];?></td>
                            <td><?php echo $order['o_price'];?></td>
                            <td><?php echo $order['o_amount'];?></td>
                            <td><?php echo $order['o_price'] * $order['o_amount'];?></td>
                        </tr>
                            <?php } ?>
                        </table>
                        <div>預約取餐時間在 <?php echo $time;?></div>
                        <div>當前訂單狀態:<?php echo $statusObj['o_status'];?>
                        </div>
                        <div>
                            更改訂單狀態<select id="orderStatusSelect<?php echo $code?>">
                                <option value="新訂單">新訂單</option>
                                <option value="備餐中">備餐中</option>
                                <option value="可取餐">可取餐</option>
                                <option value="已完成">已完成</option>
                            </select>
                            <script>
                                $("#orderStatusSelect<?php echo $code;?> option[value=<?php echo $statusObj['o_status'];?>]").prop('selected',true);
                            </script>
                        </div>
                        <?php if($statusObj['o_pay']=='T'){ ?>
                            <div style="color:blue">已付款</div>
                        <?php }else{ ?>
                            <div style="color:red;" class="payDiv<?php echo $code;?>">未付款<button type="button" class="btn btn-warning" onclick="payOn(<?php echo $code;?>)">變更為已付款</button></div>
                            
                            
                            
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
?>
<script>
$(".orderRecordBtn").click(function(){
    var n = $(this).attr('va');
    $("#orderRecord"+n).modal('toggle');
});
function payOn(id){
    if(confirm('確定嗎?確認後無法再更改!')){
        $.ajax({
            url:'./menager/changePay.php',
            data:{
                'code':id
            },
            method:'POST',
            success:function(result){
                $(".payDiv"+id).html('已付款').css('color','blue');
                alert('變更成功!');

            },
            error:function(err){
                console.log(err);
            }
        })
    }
}
</script>