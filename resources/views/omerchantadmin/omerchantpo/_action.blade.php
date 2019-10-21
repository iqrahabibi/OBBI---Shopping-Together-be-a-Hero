@if($model->status != 'Processed' && $model->status != 'Closed' && $model->status != 'Rejected')
{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
  @if($model->status == 'Checked')
    <a href="{{ $processed_url }}" class="btn btn-xs btn-primary">Process</a>
  @endif

  {!! Form::submit('Delete', ['class'=>'btn btn-xs btn-danger js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
@endif

@if($model->status == 'Processed')
  <a href="{{ $closed_url }}" class="btn btn-xs btn-primary">Close</a>
@endif
<a href="{{ $detail_url }}" class="btn btn-xs btn-info" target="_blank">Detail</a>