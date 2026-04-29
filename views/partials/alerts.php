<?php
foreach(['success','error','warning','info'] as $k):
  $msg=flash($k); if(!$msg) continue;
  $cls=['success'=>'alert-success','error'=>'alert-danger','warning'=>'alert-warning','info'=>'alert-info'][$k];
  $ico=['success'=>'bi-check-circle-fill','error'=>'bi-exclamation-circle-fill','warning'=>'bi-exclamation-triangle-fill','info'=>'bi-info-circle-fill'][$k];
?>
<div class="alert <?=$cls?> d-flex align-items-center gap-2 py-2 auto-dismiss" role="alert">
  <i class="bi <?=$ico?> flex-shrink-0"></i><span class="small"><?=h($msg)?></span>
  <button class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
<?php endforeach; ?>
