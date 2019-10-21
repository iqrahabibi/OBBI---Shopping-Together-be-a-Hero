@if($tipe == 1)
{!! Form::model($model,['url'=>$proccess,'method'=>'get','class'=>'form-inline']) !!}
    {!! Form::submit('Proccess', ['class'=>'btn btn-xs btn-primary js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
@elseif($tipe ==2)
{!! Form::model($model,['url'=>$sending,'method'=>'delete','class'=>'form-inline']) !!}
    {!! Form::submit('Sending', ['class' =>'btn btn-xs btn-info js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
@elseif($tipe == 3)
{!! Form::model($model,['url'=>$sending,'method'=>'get','class'=>'form-inline']) !!}
    {!! Form::submit('Sent', ['class' =>'btn btn-xs btn-info js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}
@endif