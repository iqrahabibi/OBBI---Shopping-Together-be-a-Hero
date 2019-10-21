{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
    <a href="{{$ubah_status}}" class="btn btn-xs btn-primary">Proccess</a> |
    {!! Form::submit('Cancelled', ['class'=>'btn btn-xs red-intense js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}