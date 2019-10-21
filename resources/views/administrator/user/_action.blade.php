{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
  {!! Form::submit('Delete', ['class'=>'btn btn-xs red-intense js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
